import { NextResponse } from "next/server";
import { z } from "zod";
import { getAppContext } from "@/lib/auth";
import { createAdminClient } from "@/lib/supabase/admin";

const passwordSchema = z
  .string()
  .min(8, "A senha deve ter pelo menos 8 caracteres.")
  .max(72, "A senha deve ter no máximo 72 caracteres.");

const createSchema = z.object({
  email: z.email("Informe um e-mail válido."),
  name: z.string().trim().min(2, "Informe o nome do usuário.").max(120),
  password: passwordSchema,
  role: z.enum(["owner", "admin", "manager", "user"]),
});

const updatePasswordSchema = z.object({
  userId: z.uuid(),
  password: passwordSchema,
});

async function requireUserAdmin() {
  const context = await getAppContext();

  if (!["owner", "admin"].includes(context.role)) {
    return { response: NextResponse.json({ error: "Sem permissão." }, { status: 403 }) } as const;
  }

  return { context } as const;
}

export async function POST(request: Request) {
  try {
    const authorization = await requireUserAdmin();
    if ("response" in authorization) return authorization.response;

    const input = createSchema.parse(await request.json());
    const admin = createAdminClient();
    const { data, error } = await admin.auth.admin.createUser({
      email: input.email,
      password: input.password,
      email_confirm: true,
      user_metadata: { name: input.name },
    });

    if (error) throw error;
    if (!data.user) throw new Error("Usuário não criado.");

    const { data: membership, error: linkError } = await admin
      .from("company_users")
      .insert({
        company_id: authorization.context.company.id,
        user_id: data.user.id,
        role: input.role,
        is_active: true,
      })
      .select("id,created_at")
      .single();

    if (linkError) {
      await admin.auth.admin.deleteUser(data.user.id);
      throw linkError;
    }

    return NextResponse.json({
      id: membership.id,
      userId: data.user.id,
      email: data.user.email,
      name: input.name,
      createdAt: membership.created_at,
    });
  } catch (error) {
    const message = error instanceof Error ? error.message : "Erro ao criar usuário.";
    return NextResponse.json({ error: message }, { status: 400 });
  }
}

export async function PATCH(request: Request) {
  try {
    const authorization = await requireUserAdmin();
    if ("response" in authorization) return authorization.response;

    const input = updatePasswordSchema.parse(await request.json());
    const admin = createAdminClient();
    const { data: membership, error: membershipError } = await admin
      .from("company_users")
      .select("id")
      .eq("company_id", authorization.context.company.id)
      .eq("user_id", input.userId)
      .maybeSingle();

    if (membershipError) throw membershipError;
    if (!membership) {
      return NextResponse.json({ error: "Usuário não pertence a esta empresa." }, { status: 404 });
    }

    const { error } = await admin.auth.admin.updateUserById(input.userId, {
      password: input.password,
    });

    if (error) throw error;
    return NextResponse.json({ success: true });
  } catch (error) {
    const message = error instanceof Error ? error.message : "Erro ao alterar senha.";
    return NextResponse.json({ error: message }, { status: 400 });
  }
}
