import { useForm, Head } from '@inertiajs/react';

export default function Login() {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    function handleSubmit(e) {
        e.preventDefault();
        post('/login');
    }

    return (
        <>
            <Head title="Login" />
            <div className="min-h-screen flex items-center justify-center bg-[var(--color-sidebar)] px-4">
                <div className="w-full max-w-sm">
                    <div className="text-center mb-8">
                        <img
                            src="/assets/canal-som-logo.png"
                            alt="Canal Som"
                            className="w-16 h-16 mx-auto rounded-xl mb-4"
                        />
                        <h1 className="text-xl font-bold text-white">InfoGate Gestão</h1>
                        <p className="text-sm text-gray-400 mt-1">Canal Som · Gestão Comercial</p>
                    </div>

                    <form
                        onSubmit={handleSubmit}
                        className="bg-[var(--color-surface)] rounded-xl p-6 space-y-4 border border-[var(--color-border)]"
                    >
                        <div>
                            <label className="block text-sm font-medium text-[var(--color-text)] mb-1">
                                E-mail
                            </label>
                            <input
                                type="email"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm text-[var(--color-text)] bg-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent"
                                autoFocus
                            />
                            {errors.email && (
                                <p className="text-xs text-[var(--color-danger)] mt-1">{errors.email}</p>
                            )}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-[var(--color-text)] mb-1">
                                Senha
                            </label>
                            <input
                                type="password"
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm text-[var(--color-text)] bg-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent"
                            />
                            {errors.password && (
                                <p className="text-xs text-[var(--color-danger)] mt-1">{errors.password}</p>
                            )}
                        </div>

                        <div className="flex items-center gap-2">
                            <input
                                type="checkbox"
                                id="remember"
                                checked={data.remember}
                                onChange={(e) => setData('remember', e.target.checked)}
                                className="rounded border-[var(--color-border)] text-[var(--color-primary)] focus:ring-[var(--color-primary)]"
                            />
                            <label htmlFor="remember" className="text-sm text-[var(--color-text-muted)]">
                                Lembrar de mim
                            </label>
                        </div>

                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full py-2.5 px-4 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition-colors disabled:opacity-50"
                        >
                            {processing ? 'Entrando...' : 'Entrar'}
                        </button>
                    </form>

                    <p className="text-center text-xs text-gray-500 mt-6">
                        InfoGate Gestão · Canal Som
                    </p>
                </div>
            </div>
        </>
    );
}
