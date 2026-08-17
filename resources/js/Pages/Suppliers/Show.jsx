import { Link, Head } from '@inertiajs/react';

export default function SupplierShow({ supplier }) {
    return (
        <>
            <Head title={supplier.name} />
            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <Link href="/cadastros/fornecedores" className="text-xs text-[var(--color-primary)] hover:underline">← Voltar</Link>
                        <h1 className="text-xl font-bold text-[var(--color-text)] mt-1">{supplier.name}</h1>
                        {supplier.trade_name && <p className="text-sm text-[var(--color-text-muted)]">{supplier.trade_name}</p>}
                    </div>
                    <Link href={`/cadastros/fornecedores/${supplier.id}/editar`} className="px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition-colors">Editar</Link>
                </div>
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-3">
                        <h2 className="text-sm font-semibold text-[var(--color-text)]">Dados</h2>
                        <dl className="grid grid-cols-2 gap-3 text-sm">
                            <div><dt className="text-[var(--color-text-muted)]">Tipo</dt><dd className="font-medium text-[var(--color-text)]">{supplier.type === 'pj' ? 'Pessoa Jurídica' : 'Pessoa Física'}</dd></div>
                            <div><dt className="text-[var(--color-text-muted)]">Documento</dt><dd className="font-medium text-[var(--color-text)]">{supplier.document || '—'}</dd></div>
                            <div><dt className="text-[var(--color-text-muted)]">Contato</dt><dd className="font-medium text-[var(--color-text)]">{supplier.contact_name || '—'}</dd></div>
                            <div><dt className="text-[var(--color-text-muted)]">Telefone</dt><dd className="font-medium text-[var(--color-text)]">{supplier.phone || '—'}</dd></div>
                            <div><dt className="text-[var(--color-text-muted)]">WhatsApp</dt><dd className="font-medium text-[var(--color-text)]">{supplier.whatsapp || '—'}</dd></div>
                            <div><dt className="text-[var(--color-text-muted)]">E-mail</dt><dd className="font-medium text-[var(--color-text)]">{supplier.email || '—'}</dd></div>
                            <div><dt className="text-[var(--color-text-muted)]">Status</dt><dd><span className={`inline-block px-2 py-0.5 rounded-full text-xs font-medium ${supplier.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>{supplier.status === 'active' ? 'Ativo' : 'Inativo'}</span></dd></div>
                        </dl>
                    </div>
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-3">
                        <h2 className="text-sm font-semibold text-[var(--color-text)]">Endereço</h2>
                        <dl className="grid grid-cols-2 gap-3 text-sm">
                            <div className="col-span-2"><dt className="text-[var(--color-text-muted)]">Logradouro</dt><dd className="font-medium text-[var(--color-text)]">{supplier.street ? `${supplier.street}, ${supplier.number || 'S/N'}` : '—'}</dd></div>
                            <div><dt className="text-[var(--color-text-muted)]">Bairro</dt><dd className="font-medium text-[var(--color-text)]">{supplier.neighborhood || '—'}</dd></div>
                            <div><dt className="text-[var(--color-text-muted)]">Cidade/UF</dt><dd className="font-medium text-[var(--color-text)]">{supplier.city ? `${supplier.city}/${supplier.state}` : '—'}</dd></div>
                            <div><dt className="text-[var(--color-text-muted)]">CEP</dt><dd className="font-medium text-[var(--color-text)]">{supplier.postal_code || '—'}</dd></div>
                        </dl>
                    </div>
                </div>
                {supplier.notes && (
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <h2 className="text-sm font-semibold text-[var(--color-text)] mb-2">Observações</h2>
                        <p className="text-sm text-[var(--color-text-muted)] whitespace-pre-wrap">{supplier.notes}</p>
                    </div>
                )}
            </div>
        </>
    );
}
