import { useForm, Link, Head } from '@inertiajs/react';
import { useState } from 'react';

export default function VehicleCreate({ customers, brands }) {
    const [models, setModels] = useState([]);
    const [loadingModels, setLoadingModels] = useState(false);
    const [newCustomer, setNewCustomer] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        customer_id: '',
        customer_name: '',
        customer_phone: '',
        vehicle_brand_id: '',
        vehicle_model_id: '',
        plate: '',
        version: '',
        year_manufacture: '',
        year_model: '',
        color: '',
        chassis: '',
        odometer: '',
        notes: '',
    });

    async function handleBrandChange(brandId) {
        setData('vehicle_brand_id', brandId);
        setData('vehicle_model_id', '');
        setModels([]);

        if (brandId) {
            setLoadingModels(true);
            try {
                const response = await fetch(`/cadastros/veiculos/modelos/${brandId}`);
                const data = await response.json();
                setModels(data);
            } catch {
                setModels([]);
            }
            setLoadingModels(false);
        }
    }

    function handleSubmit(e) {
        e.preventDefault();
        post('/cadastros/veiculos');
    }

    return (
        <>
            <Head title="Novo Veículo" />
            <div className="space-y-6 max-w-3xl">
                <div>
                    <Link href="/cadastros/veiculos" className="text-xs text-[var(--color-primary)] hover:underline">
                        ← Voltar para veículos
                    </Link>
                    <h1 className="text-xl font-bold text-[var(--color-text)] mt-1">Novo Veículo</h1>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-4">
                        <div className="flex items-center justify-between">
                            <h2 className="text-sm font-semibold text-[var(--color-text)]">Proprietário</h2>
                            <button
                                type="button"
                                onClick={() => {
                                    setNewCustomer(!newCustomer);
                                    setData('customer_id', '');
                                    setData('customer_name', '');
                                    setData('customer_phone', '');
                                }}
                                className="text-xs text-[var(--color-primary)] hover:underline"
                            >
                                {newCustomer ? 'Selecionar existente' : '+ Novo cliente'}
                            </button>
                        </div>

                        {newCustomer ? (
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Nome</label>
                                    <input
                                        type="text"
                                        value={data.customer_name}
                                        onChange={(e) => setData('customer_name', e.target.value)}
                                        className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
                                    />
                                    {errors.customer_name && <p className="text-xs text-[var(--color-danger)] mt-1">{errors.customer_name}</p>}
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Telefone</label>
                                    <input
                                        type="text"
                                        value={data.customer_phone}
                                        onChange={(e) => setData('customer_phone', e.target.value)}
                                        className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
                                    />
                                </div>
                            </div>
                        ) : (
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Cliente</label>
                                <select
                                    value={data.customer_id}
                                    onChange={(e) => setData('customer_id', e.target.value)}
                                    className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white"
                                >
                                    <option value="">Selecione...</option>
                                    {customers.map((c) => (
                                        <option key={c.id} value={c.id}>{c.name}</option>
                                    ))}
                                </select>
                                {errors.customer_id && <p className="text-xs text-[var(--color-danger)] mt-1">{errors.customer_id}</p>}
                            </div>
                        )}
                    </div>

                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-4">
                        <h2 className="text-sm font-semibold text-[var(--color-text)]">Dados do Veículo</h2>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Marca</label>
                                <select
                                    value={data.vehicle_brand_id}
                                    onChange={(e) => handleBrandChange(e.target.value)}
                                    className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white"
                                >
                                    <option value="">Selecione...</option>
                                    {brands.map((b) => (
                                        <option key={b.id} value={b.id}>{b.name}</option>
                                    ))}
                                </select>
                                {errors.vehicle_brand_id && <p className="text-xs text-[var(--color-danger)] mt-1">{errors.vehicle_brand_id}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Modelo</label>
                                <select
                                    value={data.vehicle_model_id}
                                    onChange={(e) => setData('vehicle_model_id', e.target.value)}
                                    disabled={!data.vehicle_brand_id || loadingModels}
                                    className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white disabled:opacity-50"
                                >
                                    <option value="">{loadingModels ? 'Carregando...' : 'Selecione...'}</option>
                                    {models.map((m) => (
                                        <option key={m.id} value={m.id}>{m.name}</option>
                                    ))}
                                </select>
                                {errors.vehicle_model_id && <p className="text-xs text-[var(--color-danger)] mt-1">{errors.vehicle_model_id}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Placa</label>
                                <input
                                    type="text"
                                    value={data.plate}
                                    onChange={(e) => setData('plate', e.target.value.toUpperCase())}
                                    placeholder="ABC-1234 ou ABC1D23"
                                    maxLength={8}
                                    className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
                                />
                                {errors.plate && <p className="text-xs text-[var(--color-danger)] mt-1">{errors.plate}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Versão</label>
                                <input
                                    type="text"
                                    value={data.version}
                                    onChange={(e) => setData('version', e.target.value)}
                                    className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Ano Fabricação</label>
                                <input
                                    type="number"
                                    value={data.year_manufacture}
                                    onChange={(e) => setData('year_manufacture', e.target.value)}
                                    className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Ano Modelo</label>
                                <input
                                    type="number"
                                    value={data.year_model}
                                    onChange={(e) => setData('year_model', e.target.value)}
                                    className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Cor</label>
                                <input
                                    type="text"
                                    value={data.color}
                                    onChange={(e) => setData('color', e.target.value)}
                                    className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Chassi</label>
                                <input
                                    type="text"
                                    value={data.chassis}
                                    onChange={(e) => setData('chassis', e.target.value)}
                                    className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Km</label>
                                <input
                                    type="number"
                                    value={data.odometer}
                                    onChange={(e) => setData('odometer', e.target.value)}
                                    className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
                                />
                            </div>
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
                            {processing ? 'Salvando...' : 'Cadastrar Veículo'}
                        </button>
                        <Link
                            href="/cadastros/veiculos"
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
