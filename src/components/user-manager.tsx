"use client";

import { useState } from "react";
import { KeyRound, Plus } from "lucide-react";

type UserRow = {
  id: string;
  user_id: string;
  email?: string;
  name?: string;
  role: string;
  is_active: boolean;
  created_at: string;
};

function PasswordFields({ newPassword = false }: { newPassword?: boolean }) {
  return (
    <>
      <div className="field">
        <label htmlFor="password">{newPassword ? "Nova senha" : "Senha"}</label>
        <input className="input" id="password" name="password" type="password" minLength={8} maxLength={72} autoComplete="new-password" required />
      </div>
      <div className="field">
        <label htmlFor="passwordConfirmation">Confirmar senha</label>
        <input className="input" id="passwordConfirmation" name="passwordConfirmation" type="password" minLength={8} maxLength={72} autoComplete="new-password" required />
      </div>
    </>
  );
}

export function UserManager({ initialRows }: { initialRows: UserRow[] }) {
  const [rows, setRows] = useState(initialRows);
  const [creating, setCreating] = useState(false);
  const [editing, setEditing] = useState<UserRow | null>(null);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");
  const [busy, setBusy] = useState(false);

  function validatePasswords(formData: FormData) {
    const password = String(formData.get("password") ?? "");
    const confirmation = String(formData.get("passwordConfirmation") ?? "");
    if (password !== confirmation) {
      setError("As senhas não coincidem.");
      return null;
    }
    return password;
  }

  async function createUser(formData: FormData) {
    setError("");
    setSuccess("");
    const password = validatePasswords(formData);
    if (!password) return;

    setBusy(true);
    const response = await fetch("/api/admin/users", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ name: formData.get("name"), email: formData.get("email"), role: formData.get("role"), password }),
    });
    const result = await response.json();

    if (!response.ok) {
      setError(result.error);
      setBusy(false);
      return;
    }

    setRows((current) => [{ id: result.id, user_id: result.userId, email: result.email, name: result.name, role: String(formData.get("role")), is_active: true, created_at: result.createdAt }, ...current]);
    setCreating(false);
    setSuccess("Usuário criado. Ele já pode entrar com o e-mail e a senha definidos.");
    setBusy(false);
  }

  async function updatePassword(formData: FormData) {
    if (!editing) return;
    setError("");
    setSuccess("");
    const password = validatePasswords(formData);
    if (!password) return;

    setBusy(true);
    const response = await fetch("/api/admin/users", {
      method: "PATCH",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ userId: editing.user_id, password }),
    });
    const result = await response.json();

    if (!response.ok) {
      setError(result.error);
      setBusy(false);
      return;
    }

    setEditing(null);
    setSuccess("Senha alterada com sucesso.");
    setBusy(false);
  }

  function closeModal() {
    if (busy) return;
    setCreating(false);
    setEditing(null);
    setError("");
  }

  return (
    <>
      <div className="page-heading">
        <div><h1>Usuários</h1><div className="muted">Acessos e permissões da empresa</div></div>
        <button className="btn btn-primary" onClick={() => { setCreating(true); setError(""); setSuccess(""); }}><Plus size={17} /> Novo usuário</button>
      </div>
      {error && <p className="error">{error}</p>}
      {success && <p className="success">{success}</p>}

      <section className="card">
        <div className="table-wrap">
          <table>
            <thead><tr><th>Usuário</th><th>Perfil</th><th>Ativo</th><th>Desde</th><th>Ações</th></tr></thead>
            <tbody>
              {rows.map((row) => (
                <tr key={row.id}>
                  <td><strong>{row.name || row.email || row.user_id}</strong>{(row.name || row.email) && <div className="muted">{row.email || row.user_id}</div>}</td>
                  <td><span className="badge">{row.role}</span></td>
                  <td>{row.is_active ? "Sim" : "Não"}</td>
                  <td>{new Intl.DateTimeFormat("pt-BR").format(new Date(row.created_at))}</td>
                  <td><button className="btn btn-secondary" onClick={() => { setEditing(row); setError(""); setSuccess(""); }}><KeyRound size={16} /> Alterar senha</button></td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>

      {creating && (
        <div className="modal-backdrop">
          <form className="modal" action={createUser}>
            <div className="modal-header"><strong>Novo usuário</strong></div>
            <div className="modal-body"><div className="form-grid">
              <div className="field"><label htmlFor="name">Nome</label><input className="input" id="name" name="name" maxLength={120} required /></div>
              <div className="field"><label htmlFor="email">E-mail</label><input className="input" id="email" type="email" name="email" required /></div>
              <PasswordFields />
              <div className="field"><label htmlFor="role">Perfil</label><select className="select" id="role" name="role"><option value="user">Usuário</option><option value="manager">Gerente</option><option value="admin">Administrador</option></select></div>
            </div></div>
            <div className="modal-footer"><button type="button" className="btn btn-secondary" onClick={closeModal}>Cancelar</button><button className="btn btn-primary" disabled={busy}>{busy ? "Criando..." : "Criar usuário"}</button></div>
          </form>
        </div>
      )}

      {editing && (
        <div className="modal-backdrop">
          <form className="modal" action={updatePassword}>
            <div className="modal-header"><strong>Alterar senha</strong></div>
            <div className="modal-body"><p className="muted">Defina uma nova senha para {editing.email || editing.user_id}.</p><div className="form-grid"><PasswordFields newPassword /></div></div>
            <div className="modal-footer"><button type="button" className="btn btn-secondary" onClick={closeModal}>Cancelar</button><button className="btn btn-primary" disabled={busy}>{busy ? "Salvando..." : "Salvar nova senha"}</button></div>
          </form>
        </div>
      )}
    </>
  );
}
