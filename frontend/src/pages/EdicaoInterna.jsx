import { useEffect, useState, useMemo } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { useGlobalData } from '../hooks/useGlobalData';
import {
    ArrowLeft, BookOpen, Calendar, ChevronLeft, ChevronRight,
    ExternalLink, Music, Users, Mic, BookMarked
} from 'lucide-react';
import '../styles/edicao-interna.css';

import { Helmet } from 'react-helmet-async';

const EdicaoInterna = () => {
    const { id } = useParams();
    const navigate = useNavigate();
    const { data, loading: globalLoading } = useGlobalData();
    const config = data?.configuracoes || {};
    const edicoes = data?.edicoes || [];

    const [edicaoDetails, setEdicaoDetails] = useState(null);
    const [loadingDetails, setLoadingDetails] = useState(false);

    const matchEdicao = useMemo(() => {
        if (!edicoes.length) return null;
        return edicoes.find(e => {
            const num = e.numero_edicao.replace('Edição #', '').trim();
            return num === id;
        });
    }, [edicoes, id]);

    const currentIndex = useMemo(() => {
        if (!matchEdicao) return -1;
        return edicoes.findIndex(e => e === matchEdicao);
    }, [edicoes, matchEdicao]);

    const prevEdicao = currentIndex > 0 ? edicoes[currentIndex - 1] : null;
    const nextEdicao = currentIndex >= 0 && currentIndex < edicoes.length - 1 ? edicoes[currentIndex + 1] : null;

    const toPath = (ed) => `/edicoes/edicao/${ed.numero_edicao.replace('Edição #', '').trim()}`;

    useEffect(() => {
        if (globalLoading) return;
        if (!matchEdicao) {
            navigate('/edicoes');
            return;
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });

        const fetchDetails = async () => {
            setLoadingDetails(true);
            try {
                const api_key = import.meta.env.VITE_API_KEY || 'SUA_API_KEY_AQUI';
                const baseUrl = import.meta.env.VITE_API_URL || '/api';
                const endpoint_url = `${baseUrl}/endpoint.php?action=get_edicao&api_key=${api_key}&id=${matchEdicao.id}`;
                const res = await fetch(endpoint_url);
                const json = await res.json();
                if (json.success && json.data) {
                    setEdicaoDetails(json.data);
                }

                try {
                    await fetch(`${baseUrl}/views_count.php?id=${matchEdicao.id}`);
                } catch (e) {
                    console.error("Failed to register view count:", e);
                }
            } catch (err) {
                console.error("Failed to fetch edition details:", err);
            } finally {
                setLoadingDetails(false);
            }
        };

        fetchDetails();
    }, [id, matchEdicao, navigate, globalLoading]);

    if (globalLoading || (loadingDetails && !edicaoDetails)) {
        return (
            <div className="loading-spinner">
                <BookOpen size={48} style={{ opacity: 0.4, marginBottom: 20 }} />
                <p>Carregando edição...</p>
            </div>
        );
    }

    if (!matchEdicao) return null;

    const ed = edicaoDetails || matchEdicao;
    const corHeader = config.cor_header || '#1E1E1E';
    const corTexto = config.cor_texto_header || '#fff';

    const resumosTccs = ed.resumos_tccs
        ? ed.resumos_tccs.split(/\r?\n/).filter(Boolean)
        : [];

    const editorias = [
        { label: 'Entrevista', value: ed.editoria_entrevista },
        { label: 'Afinal, o que é?', value: ed.editoria_afinal },
        { label: 'Look In', value: ed.editoria_lookin },
        { label: 'Conhecendo o Mestre', value: ed.editoria_mestre },
        { label: 'Prata da Casa', value: ed.editoria_prata },
    ].filter(e => e.value);

    const credits = [
        { role: 'Professora', name: ed.exp_professor },
        { role: 'Editora-chefe', name: ed.exp_editor_chefe },
        { role: 'Redação', name: ed.exp_redacao },
        { role: 'Diagramação', name: ed.exp_diagramacao },
        { role: 'Revisão', name: ed.exp_revisao },
        { role: 'Fotografia', name: ed.exp_fotografia },
        { role: 'Comissão do Site', name: ed.exp_comissao_site },
        { role: 'Colaboradores', name: ed.exp_colaboradores },
    ].filter(c => c.name);

    return (
        <div className="edicao-interna-page">
            <Helmet>
                <title>{ed.numero_edicao} | Nome do Projeto</title>
                <meta name="description" content={ed.resumo || 'Confira os detalhes desta edição da publicacao.'} />
                <meta property="og:title" content={`${ed.numero_edicao} | Nome do Projeto`} />
                <meta property="og:description" content={ed.resumo || 'Confira os detalhes desta edição.'} />
                <meta property="og:image" content={ed.url_capa} />
            </Helmet>
            <section className="edicao-hero" style={{ backgroundColor: corHeader }}>
                <div className="edicao-hero-inner">

                    <div className="edicao-hero-content">
                        <div className="edicao-cover-wrapper">
                            <img src={ed.url_capa} alt={ed.numero_edicao} className="edicao-cover" />
                        </div>
                        <div className="edicao-info" style={{ color: corTexto }}>
                            <span className="edicao-badge">{ed.views} visualizações</span>
                            <h1>{ed.numero_edicao}</h1>
                            <div className="edicao-meta">
                                <Calendar size={16} />
                                <span>{ed.data_lancamento}</span>
                            </div>
                            <p className="resumo">{ed.resumo}</p>

                            <div className="edicao-cta-buttons">
                                {ed.url_flipbook && (
                                    <a href={ed.url_flipbook} target="_blank" rel="noreferrer" className="ui-btn ui-btn-light">
                                        <BookOpen size={18} />
                                        <span>Ler edição</span>
                                    </a>
                                )}
                                {ed.url_pdf && (
                                    <a href={ed.url_pdf} target="_blank" rel="noreferrer" className="ui-btn" style={{ background: 'rgba(255,255,255,0.15)', color: corTexto, border: '1px solid rgba(255,255,255,0.3)' }}>
                                        <ExternalLink size={18} />
                                        <span>PDF</span>
                                    </a>
                                )}
                                {ed.url_playlist && (
                                    <a href={ed.url_playlist} target="_blank" rel="noreferrer" className="ui-btn ui-btn-spotify">
                                        <svg fill="#000000" width="24px" height="24px" viewBox="-2 -2 24 24" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMinYMin" class="jam jam-spotify"><path d='M9.992 0C4.474 0 0 4.474 0 9.992c0 5.518 4.474 9.992 9.992 9.992 5.518 0 9.992-4.474 9.992-9.992C19.984 4.474 15.51 0 9.992 0zm4.348 15.683c-.127.329-.355.512-.59.512a.518.518 0 0 1-.344-.141c-1.796-1.588-3.87-1.843-5.294-1.778-1.578.073-2.735.544-2.747.549-.363.15-.74-.174-.839-.724-.1-.55.114-1.119.477-1.27.052-.022 1.297-.534 3.029-.62a8.939 8.939 0 0 1 2.917.32 8.09 8.09 0 0 1 3.146 1.737c.326.289.436.922.245 1.415zm1.27-3.063c-.15.329-.42.512-.699.512a.677.677 0 0 1-.407-.141c-2.127-1.588-4.584-1.843-6.271-1.778-1.87.073-3.24.544-3.253.549-.431.15-.876-.174-.995-.724-.118-.55.135-1.119.566-1.27.061-.022 1.536-.534 3.587-.62 1.208-.051 2.37.057 3.456.32 1.374.333 2.628.917 3.726 1.737.386.288.516.922.29 1.415zm.782-2.996a.958.958 0 0 1-.5-.142C10.835 6.404 4.276 8.234 4.21 8.252c-.528.153-1.075-.17-1.22-.721-.146-.551.165-1.12.693-1.272.076-.022 1.885-.534 4.4-.62a18.63 18.63 0 0 1 4.24.32c1.686.333 3.223.917 4.57 1.738.474.288.633.921.357 1.414a.985.985 0 0 1-.858.513z' /></svg>
                                        <span>Playlist</span>
                                    </a>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {ed.url_flipbook && (
                <section className="edicao-flipbook-section">
                    <div className="edicao-flipbook-wrap">
                        <div className="edicao-flipbook-container">
                            <iframe
                                src={ed.url_flipbook}
                                title={`Leitura: ${ed.numero_edicao}`}
                                seamless
                                scrolling="yes"
                                frameBorder="0"
                                allowTransparency
                                allowFullScreen
                            />
                        </div>
                    </div>
                </section>
            )}

            {(editorias.length > 0 || credits.length > 0 || resumosTccs.length > 0) && (
                <section className="edicao-details-section">
                    <div className="edicao-details-inner">

                        {editorias.length > 0 && (
                            <div className="edicao-editorias">
                                <div className="edicao-section-title">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000"><path d="M852-212 732-332l56-56 120 120-56 56ZM708-692l-56-56 120-120 56 56-120 120Zm-456 0L132-812l56-56 120 120-56 56ZM108-212l-56-56 120-120 56 56-120 120Zm246-75 126-76 126 77-33-144 111-96-146-13-58-136-58 135-146 13 111 97-33 143ZM233-120l65-281L80-590l288-25 112-265 112 265 288 25-218 189 65 281-247-149-247 149Zm247-361Z" /></svg>
                                    <h2>Editorias Clássicas</h2>
                                </div>
                                {editorias.map((e, i) => (
                                    <div key={i} className="ed-detail-item">
                                        <strong>{e.label}</strong>
                                        <p>{e.value}</p>
                                    </div>
                                ))}
                                {resumosTccs.length > 0 && (
                                    <div className="ed-detail-item">
                                        <strong>Resumos de TCCs</strong>
                                        {resumosTccs.map((line, i) => {
                                            const sepIdx = line.indexOf(' - Por ');
                                            if (sepIdx !== -1) {
                                                return <p key={i}><b>{line.slice(0, sepIdx)}</b>{line.slice(sepIdx)}</p>;
                                            }
                                            return <p key={i}>{line}</p>;
                                        })}
                                    </div>
                                )}
                            </div>
                        )}

                        {credits.length > 0 && (
                            <div className="edicao-creditos">
                                <div className="edicao-section-title">
                                    <Users size={20} />
                                    <h2>Expediente</h2>
                                </div>
                                {credits.map((c, i) => (
                                    <div key={i} className="credit-item">
                                        <span className="credit-role">{c.role}</span>
                                        <span className="credit-name">{c.name}</span>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                </section>
            )}

            <div className="edicao-nav">
                <div className="edicao-nav-inner">
                    {nextEdicao ? (
                        <Link to={toPath(nextEdicao)} className="edicao-nav-btn edicao-nav-prev">
                            <ChevronLeft size={20} />
                            <div>
                                <small>Anterior</small>
                                <strong>{nextEdicao.numero_edicao}</strong>
                            </div>
                        </Link>
                    ) : <span />}

                    {prevEdicao ? (
                        <Link to={toPath(prevEdicao)} className="edicao-nav-btn edicao-nav-next">
                            <div>
                                <small>Próxima</small>
                                <strong>{prevEdicao.numero_edicao}</strong>
                            </div>
                            <ChevronRight size={20} />
                        </Link>
                    ) : <span />}
                </div>
            </div>
        </div>
    );
};

export default EdicaoInterna;
