import { useForm, Link, Head } from '@inertiajs/react';

export default function ProductCreate() {
    const { data, setData, post, processing, errors } = useForm({
        type: 'product',
        name: '',
        sku: '',
        barcode: '',
        description: '',
        unit: 'UN',
        cost_price: '',
        sale_price: '',
        stock_quantity: '',
        stock_minimum: '',
        category: '',
        brand: '',
        ncm: '',
        status: 'active',
    });

    function handleSubmit(e) {
        e.preventDefault();
        post('/cadastros/produtos');
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
            <Head title="Novo Produto/Serviço" />
            <div className="space-y-6 max-w-3xl">
                <div>
                    <Link href="/cadastros/produtos" className="text-xs text-[var(--color-primary)] hover:underline">← Voltar</Link>
                    <h1 className="text-xl font-bold text-[var(--color-text)] mt-1">Novo Produto / Serviço</h1>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-4">
                        <h2 className="text-sm font-semibold text-[var(--color-text)]">Informações Gerais</h2>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Tipo</label>
                                <select value={data.type} onChange={(e) => setData('type', e.target.value)} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white">
                                    <option value="product">Produto</option>
                                    <option value="service">Serviço</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Status</label>
                                <select value={data.status} onChange={(e) => setData('status', e.target.value)} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white">
                                    <option value="active">Ativo</option>
                                    <option value="inactive">Inativo</option>
                                </select>
                            </div>
                            <Field label="Nome" name="name" />
                            <Field label="SKU" name="sku" />
                            <Field label="Código de Barras" name="barcode" />
                            <Field label="Unidade" name="unit" placeholder="UN, KG, MT, HR..." />
                            <Field label="Categoria" name="category" />
                            <Field label="Marca" name="brand" />
                            <Field label="NCM" name="ncm" />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Descrição</label>
                            <textarea value={data.description} onChange={(e) => setData('description', e.target.value)} rows={3} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]" />
                        </div>
                    </div>

                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-4">
                        <h2 className="text-sm font-semibold text-[var(--color-text)]">Preços e Estoque</h2>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <Field label="Preço de Custo (R$)" name="cost_price" type="number" step="0.01" />
                            <Field label="Preço de Venda (R$)" name="sale_price" type="number" step="0.01" />
                            {data.type === 'product' && (
                                <>
                                    <Field label="Quantidade em Estoque" name="stock_quantity" type="number" step="0.01" />
                                    <Field label="Estoque Mínimo" name="stock_minimum" type="number" step="0.01" />
                                </>
                            )}
                        </div>
                    </div>

                    <div className="flex gap-3">
                        <button type="submit" disabled={processing} className="px-6 py-2.5 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition-colors disabled:opacity-50">
                            {processing ? 'Salvando...' : 'Salvar'}
                        </button>
                        <Link href="/cadastros/produtos" className="px-6 py-2.5 rounded-lg border border-[var(--color-border)] text-sm text-[var(--color-text-muted)] hover:bg-gray-50 transition-colors">
                            Cancelar
                        </Link>
                    </div>
                </form>
            </div>
        </>
    );
}
