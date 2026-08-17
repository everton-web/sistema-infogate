import { useForm, Link, Head } from '@inertiajs/react';

export default function CustomerCreate() {
    const { data, setData, post, processing, errors } = useForm({
        type: 'pf',
        name: '',
        trade_name: '',
        document: '',
        state_registration: '',
        phone: '',
        whatsapp: '',
        email: '',
        postal_code: '',
        street: '',
        number: '',
        complement: '',
        neighborhood: '',
        city: '',
        state: '',
        notes: '',
        status: 'active',
    });

    function handleSubmit(e) {
        e.preventDefault();
        post('/cadastros/clientes');
    }

    function Field({ label, name, type = 'text', ...props }) {
        return (
            <div>
                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">{label}</label>
                <input
                    type={type}
                    value={data[name]}
                    onChange={(e) => setData(name, e.target.value)}
                    className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
                    {...props}
                />
                {errors[name] && <p className="text-xs text-[var(--color-danger)] mt-1">{errors[name]}</p>}
            </div>
        );
    }

    return (
        <>
            <Head title="Novo Cliente" />
            <div className="space-y-6 max-w-3xl">
                <div>
                    <Link href="/cadastros/clientes" className="text-xs text-[var(--color-primary)] hover:underline">
                        ← Voltar para clientes
                    </Link>
                    <h1 className="text-xl font-bold text-[var(--color-text)] mt-1">Novo Cliente</h1>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-4">
                        <h2 className="text-sm font-semibold text-[var(--color-text)]">Dados Pessoais</h2>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Tipo</label>
                                <select
                                    value={data.type}
                                    onChange={(e) => setData('type', e.target.value)}
                                    className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white"
                                >
                                    <option value="pf">Pessoa Física</option>
                                    <option value="pj">Pessoa Jurídica</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Status</label>
                                <select
                                    value={data.status}
                                    onChange={(e) => setData('status', e.target.value)}
                                    className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white"
                                >
                                    <option value="active">Ativo</option>
                                    <option value="inactive">Inativo</option>
                                </select>
                            </div>
                            <Field label={data.type === 'pf' ? 'Nome Completo' : 'Razão Social'} name="name" />
                            {data.type === 'pj' && <Field label="Nome Fantasia" name="trade_name" />}
                            <Field label={data.type === 'pf' ? 'CPF' : 'CNPJ'} name="document" />
                            {data.type === 'pj' && <Field label="Inscrição Estadual" name="state_registration" />}
                            <Field label="Telefone" name="phone" />
                            <Field label="WhatsApp" name="whatsapp" />
                            <Field label="E-mail" name="email" type="email" />
                        </div>
                    </div>

                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-4">
                        <h2 className="text-sm font-semibold text-[var(--color-text)]">Endereço</h2>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <Field label="CEP" name="postal_code" />
                            <Field label="Logradouro" name="street" />
                            <Field label="Número" name="number" />
                            <Field label="Complemento" name="complement" />
                            <Field label="Bairro" name="neighborhood" />
                            <Field label="Cidade" name="city" />
                            <Field label="UF" name="state" maxLength={2} />
                        </div>
                    </div>

                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-4">
                        <h2 className="text-sm font-semibold text-[var(--color-text)]">Observações</h2>
                        <textarea
                            value={data.notes}
                            onChange={(e) => setData('notes', e.target.value)}
                            rows={3}
                            className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
                        />
                    </div>

                    <div className="flex gap-3">
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-6 py-2.5 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition-colors disabled:opacity-50"
                        >
                            {processing ? 'Salvando...' : 'Salvar Cliente'}
                        </button>
                        <Link
                            href="/cadastros/clientes"
                            className="px-6 py-2.5 rounded-lg border border-[var(--color-border)] text-sm text-[var(--color-text-muted)] hover:bg-gray-50 transition-colors"
                        >
                            Cancelar
                        </Link>
                    </div>
                </form>
            </div>
        </>
    );
}
