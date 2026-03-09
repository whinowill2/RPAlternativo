import { useState } from 'react';
import { useGlobalData } from '../hooks/useGlobalData';
import { Mail, Linkedin, Instagram, X } from 'lucide-react';
import { Helmet } from 'react-helmet-async';
import '../styles/expediente.css';

const Expediente = () => {
    const { data, loading } = useGlobalData();
    const config = data?.configuracoes || {};
    const expediente = data?.expediente || [];

    const [modalEmail, setModalEmail] = useState(null);

    if (loading) {
        return <div className="loading-spinner">Carregando...</div>;
    }

    let editorChefe = null;
    let outrosMembros = [];

    if (expediente.length > 0) {
        const editorIndex = expediente.findIndex(m =>
            m.cargo.toLowerCase() === 'editora-chefe' ||
            m.cargo.toLowerCase() === 'editor-chefe'
        );

        if (editorIndex !== -1) {
            editorChefe = expediente[editorIndex];
            outrosMembros = expediente.filter((_, idx) => idx !== editorIndex);
        } else {
            outrosMembros = [...expediente];
        }
    }

    const openModal = (email) => setModalEmail(email);
    const closeModal = () => setModalEmail(null);

    return (
        <div className="expediente-page-wrapper">
            <Helmet>
                <title>Expediente</title>
                <meta name="description" content="Conheça a equipe responsável por dar vida à Revista nesta edição: editores, redatores e todo o conselho." />
                <meta property="og:title" content="Expediente" />
                <meta property="og:description" content="Conheça a equipe responsável por dar vida à Revista nesta edição: editores, redatores e todo o conselho." />
            </Helmet>
            {editorChefe && (
                <section className="editor-in-chief" style={{ backgroundColor: config.cor_header || '#1E1E1E', color: config.cor_texto_header || '#fff' }}>
                    <img src={editorChefe.url_foto} alt={`Foto de ${editorChefe.nome}`} className="profile-pic large" />
                    <div className="info">
                        <h2 className="name">{editorChefe.nome}</h2>
                        <p className="role">{editorChefe.cargo}</p>
                        <div className="bio" dangerouslySetInnerHTML={{ __html: editorChefe.bio.replace(/\n/g, '<br />') }} />

                        <div className="contact-buttons-2">
                            {editorChefe.email && (
                                <button
                                    onClick={() => openModal(editorChefe.email)}
                                    className="btn contact-btn"
                                    style={{ backgroundColor: config.cor_texto_header || '#fff', color: config.cor_header || '#1E1E1E' }}
                                >
                                    <Mail size={20} />
                                    <span>Email</span>
                                </button>
                            )}
                            {editorChefe.linkedin && (
                                <a
                                    href={editorChefe.linkedin}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="btn icon-btn"
                                    style={{ backgroundColor: config.cor_texto_header || '#fff', color: config.cor_header || '#1E1E1E' }}
                                >
                                    <Linkedin size={20} />
                                </a>
                            )}
                            {editorChefe.instagram && (
                                <a
                                    href={editorChefe.instagram}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="btn icon-btn"
                                    style={{ backgroundColor: config.cor_texto_header || '#fff', color: config.cor_header || '#1E1E1E' }}
                                >
                                    <Instagram size={20} />
                                </a>
                            )}
                        </div>
                    </div>
                </section>
            )}

            <section className="team-grid container">
                {outrosMembros.map((membro, idx) => (
                    <article key={idx} className="team-member-card">
                        <img src={membro.url_foto} alt={`Foto de ${membro.nome}`} className="profile-pic" />
                        <div className="info">
                            <h3 className="name text-dark">{membro.nome}</h3>
                            <p className="role text-dark-muted">{membro.cargo}</p>

                            <div className="contact-buttons">
                                {membro.email && (
                                    <button
                                        onClick={() => openModal(membro.email)}
                                        className="btn contact-btn"
                                        style={{ backgroundColor: config.cor_botoes || '#1E1E1E', color: config.cor_texto_botoes || '#fff' }}
                                    >
                                        <Mail size={20} />
                                        <span>Email</span>
                                    </button>
                                )}
                                {membro.instagram && (
                                    <a
                                        href={membro.instagram}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="btn icon-btn stroke-btn"
                                        style={{ borderColor: config.cor_header || '#1E1E1E', color: config.cor_header || '#1E1E1E' }}
                                    >
                                        <Instagram size={20} />
                                    </a>
                                )}
                                {membro.linkedin && (
                                    <a
                                        href={membro.linkedin}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="btn icon-btn stroke-btn"
                                        style={{ borderColor: config.cor_header || '#1E1E1E', color: config.cor_header || '#1E1E1E' }}
                                    >
                                        <Linkedin size={20} />
                                    </a>
                                )}
                            </div>
                        </div>
                    </article>
                ))}
            </section>

            <div className={`modal-overlay ${modalEmail ? 'is-visible' : ''}`} onClick={closeModal}>
                <div className="contact-modal" onClick={e => e.stopPropagation()}>
                    <h3 className="modal-title text-dark">Email</h3>
                    <p id="modal-email" className="modal-email-text">{modalEmail}</p>
                    <button className="modal-close-btn" onClick={closeModal}><X size={28} /></button>
                </div>
            </div>
        </div>
    );
};

export default Expediente;
