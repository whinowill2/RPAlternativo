import { useState, useMemo, useRef, useEffect } from 'react';
import { useGlobalData } from '../hooks/useGlobalData';
import { Link } from 'react-router-dom';
import { Search, BookOpen, ArrowRight, X } from 'lucide-react';
import Button from '../components/Button';
import { Helmet } from 'react-helmet-async';
import '../styles/edicoes.css';

const CACHE_KEY = 'rpa_global_data_v1';

const Edicoes = () => {
    const { data, loading } = useGlobalData();
    const config = data?.configuracoes || {};
    const todasEdicoes = data?.edicoes || [];

    const [selectedYear, setSelectedYear] = useState('todos');
    const [searchQuery, setSearchQuery] = useState('');
    const pillsRef = useRef(null);

    useEffect(() => {
        const handleWheel = (e) => {
            if (pillsRef.current && e.deltaY !== 0) {
                e.preventDefault();
                pillsRef.current.scrollLeft += e.deltaY;
            }
        };

        const el = pillsRef.current;
        if (el) {
            el.addEventListener('wheel', handleWheel, { passive: false });
        }
        return () => {
            if (el) {
                el.removeEventListener('wheel', handleWheel);
            }
        };
    }, []);

    const edicaoMaisRecente = todasEdicoes[0];
    const edicoesAnteriores = todasEdicoes.slice(1);

    const availableYears = useMemo(() => {
        const years = new Set();
        todasEdicoes.forEach(ed => {
            const match = ed.data_lancamento.match(/(\d{4})/);
            if (match) years.add(match[0]);
        });
        return Array.from(years).sort((a, b) => b - a);
    }, [todasEdicoes]);

    const filteredEdicoes = useMemo(() => {
        let list = edicoesAnteriores;
        if (selectedYear !== 'todos') {
            list = list.filter(ed => {
                const match = ed.data_lancamento.match(/(\d{4})/);
                return match && match[0] === selectedYear;
            });
        }
        if (searchQuery.trim()) {
            const q = searchQuery.toLowerCase();
            list = list.filter(ed => {
                return Object.values(ed).some(val =>
                    val && typeof val === 'string' && val.toLowerCase().includes(q)
                );
            });
        }
        return list;
    }, [edicoesAnteriores, selectedYear, searchQuery]);

    if (loading) {
        return (
            <div className="loading-spinner">
                <BookOpen size={48} style={{ opacity: 0.3, marginBottom: 16 }} />
                <p>Carregando edições...</p>
            </div>
        );
    }

    return (
        <div className="edicoes-page-wrapper">
            <Helmet>
                <title>Edições Anteriores | Nome do Projeto</title>
                <meta name="description" content="Navegue por todas as edições passadas do Projeto. Leia artigos, crônicas, entrevistas e o acervo completo." />
                <meta property="og:title" content="Edições Anteriores | Nome do Projeto" />
                <meta property="og:description" content="Navegue por todas as edições passadas do Projeto. Leia artigos, crônicas, entrevistas e o acervo completo." />
            </Helmet>
            {edicaoMaisRecente && (
                <section
                    className="edicoes-hero"
                    style={{ backgroundColor: config.cor_header || '#1E1E1E', color: config.cor_texto_header || '#fff' }}
                >
                    <div className="edicoes-hero-inner">
                        <img
                            src={edicaoMaisRecente.url_capa}
                            alt={`Capa da ${edicaoMaisRecente.numero_edicao}`}
                            className="edicoes-hero-cover"
                        />
                        <div className="edicoes-hero-info">
                            <span className="edicoes-badge">Edição mais recente</span>
                            <h1>{edicaoMaisRecente.numero_edicao}</h1>
                            <p className="edicoes-hero-date">{edicaoMaisRecente.data_lancamento}</p>
                            <span className="edicoes-badge">{edicaoMaisRecente.views} visualizações</span>
                            <p className="edicoes-hero-resumo">{edicaoMaisRecente.resumo}</p>
                            <Button
                                to={`/edicoes/edicao/${edicaoMaisRecente.numero_edicao.replace('Edição #', '').trim()}`}
                                variant="light"
                                icon={ArrowRight}
                            >
                                Ler Edição
                            </Button>
                        </div>
                    </div>
                </section>
            )}

            <div className="edicoes-filters-area">
                <div className="edicoes-search-wrap">
                    <Search size={18} className="edicoes-search-icon" />
                    <input
                        type="text"
                        placeholder="Edições, Docentes, Colaboradores..."
                        value={searchQuery}
                        onChange={e => setSearchQuery(e.target.value)}
                        className="edicoes-search-input"
                    />
                    {searchQuery && (
                        <button className="edicoes-search-clear" onClick={() => setSearchQuery('')}>
                            <X size={16} />
                        </button>
                    )}
                </div>

                <div className="edicoes-pills-scroll" ref={pillsRef}>
                    {['todos', ...availableYears].map(year => (
                        <button
                            key={year}
                            className={`edicoes-pill${selectedYear === year ? ' active' : ''}`}
                            onClick={() => setSelectedYear(year)}
                        >
                            {year === 'todos' ? 'Todas' : year}
                        </button>
                    ))}
                </div>
            </div>

            <section className="edicoes-grid-section">
                {filteredEdicoes.length === 0 ? (
                    <div className="edicoes-empty">
                        <BookOpen size={64} style={{ opacity: 0.2, marginBottom: 16 }} />
                        <h3>Nenhuma edição encontrada</h3>
                        <p>Tente outro ano ou limpe a busca.</p>
                        <button
                            className="edicoes-pill active"
                            style={{ marginTop: 16 }}
                            onClick={() => { setSelectedYear('todos'); setSearchQuery(''); }}
                        >
                            Limpar filtros
                        </button>
                    </div>
                ) : (
                    <div className="edicoes-grid">
                        {filteredEdicoes.map((ed, idx) => {
                            const num = ed.numero_edicao.replace('Edição #', '').trim();
                            return (
                                <Link
                                    key={idx}
                                    to={`/edicoes/edicao/${num}`}
                                    className="edicao-card"
                                >
                                    <div className="edicao-card-cover-wrapper">
                                        <img
                                            src={ed.url_capa}
                                            alt={`Capa ${ed.numero_edicao}`}
                                            className="edicao-card-cover"
                                        />
                                        <div className="edicao-card-overlay">
                                            <span>Ler edição →</span>
                                        </div>
                                    </div>
                                    <div className="edicao-card-info">
                                        <strong>{ed.numero_edicao}</strong>
                                        <span>{ed.data_lancamento}</span>
                                    </div>
                                </Link>
                            );
                        })}
                    </div>
                )}
            </section>
        </div>
    );
};

export default Edicoes;
