import{createClient as createSupabaseClient}from"@supabase/supabase-js";import{getSupabaseEnv}from"./env";
export function createAdminClient(){const{url}=getSupabaseEnv();const key=process.env.SUPABASE_SECRET_KEY;if(!key)throw new Error("SUPABASE_SECRET_KEY não configurada.");return createSupabaseClient(url,key,{auth:{autoRefreshToken:false,persistSession:false}})}
