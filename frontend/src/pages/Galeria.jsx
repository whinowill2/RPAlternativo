import { useState, useMemo, useEffect } from 'react';
import { useGlobalData } from '../hooks/useGlobalData';
import { X } from 'lucide-react';
import classNames from 'classnames';
import { Helmet } from 'react-helmet-async';
import '../styles/galeria.css';

const Galeria = () => {
    const { data, loading } = useGlobalData();
    const config = data?.configuracoes || {};
    const galeria = data?.galeria || [];

    const [filter, setFilter] = useState('todas');
    const [selectedEdition, setSelectedEdition] = useState('');
    const [selectedPhoto, setSelectedPhoto] = useState(null);

    const { heroImage, allEditions, photosByEdition } = useMemo(() => {
        let heroImage = null;
        let photosByEdition = {};
        const editionsSet = new Set();

        galeria.forEach(item => {
            if (item.is_hero) {
                heroImage = item;
            } else {
                const ed = item.edicao || 'Geral';
                editionsSet.add(ed);
                if (!photosByEdition[ed]) {
                    photosByEdition[ed] = [];
                }
                photosByEdition[ed].push(item);
            }
        });

        Object.keys(photosByEdition).forEach(ed => {
            photosByEdition[ed].sort(() => Math.random() - 0.5);
        });

        const allEditions = Array.from(editionsSet).sort((a, b) => parseInt(b) - parseInt(a));

        return { heroImage, allEditions, photosByEdition };
    }, [galeria]);

    useEffect(() => {
        if (!selectedEdition && allEditions.length > 0) {
            setSelectedEdition(allEditions[0]);
        }
    }, [allEditions, selectedEdition]);

    useEffect(() => {
        setFilter('todas');
    }, [selectedEdition]);

    const editionImages = useMemo(() => {
        if (!selectedEdition) return [];
        return photosByEdition[selectedEdition] || [];
    }, [selectedEdition, photosByEdition]);

    const categories = useMemo(() => {
        const catSet = new Set();
        editionImages.forEach(img => {
            if (img.categoria) {
                catSet.add(img.categoria);
            }
        });
        return Array.from(catSet).sort();
    }, [editionImages]);

    const filteredImages = useMemo(() => {
        if (filter === 'todas') return editionImages;
        return editionImages.filter(img => img.categoria && img.categoria.toLowerCase() === filter.toLowerCase());
    }, [filter, editionImages]);

    if (loading) {
        return <div className="loading-spinner">Carregando...</div>;
    }

    return (
        <div className="galeria-page-wrapper">
            <Helmet>
                <title>Galeria</title>
                <meta name="description" content="Acesse a galeria de fotos das edições e eventos da Revista." />
                <meta property="og:title" content="Galeria" />
                <meta property="og:description" content="Acesse a galeria de fotos das edições e eventos da Revista." />
            </Helmet>
            {heroImage && (
                <section className="slider-wrapper">
                    <div className="slider">
                        <img src={heroImage.url_imagem} alt="Foto de Destaque da Galeria" />
                    </div>
                </section>
            )}

            {allEditions.length > 0 && (
                <section className="filter-buttons" style={{ marginBottom: categories.length > 1 ? '10px' : '30px' }}>
                    {allEditions.map(ed => (
                        <button
                            key={ed}
                            className={classNames('filter-btn', { active: selectedEdition === ed })}
                            onClick={() => setSelectedEdition(ed)}
                            style={{
                                borderColor: config.cor_header || '#1E1E1E',
                                color: selectedEdition === ed ? (config.cor_texto_botoes || '#fff') : (config.cor_header || '#1E1E1E'),
                                backgroundColor: selectedEdition === ed ? (config.cor_botoes || '#1E1E1E') : 'transparent'
                            }}
                        >
                            Edição {ed}
                        </button>
                    ))}
                </section>
            )}

            {categories.length > 1 && (
                <section className="filter-buttons">
                    <button
                        className={classNames('filter-btn', { active: filter === 'todas' })}
                        onClick={() => setFilter('todas')}
                        style={{
                            borderColor: config.cor_header || '#1E1E1E',
                            color: filter === 'todas' ? (config.cor_texto_botoes || '#fff') : (config.cor_header || '#1E1E1E'),
                            backgroundColor: filter === 'todas' ? (config.cor_botoes || '#1E1E1E') : 'transparent'
                        }}
                    >
                        Todas
                    </button>
                    {categories.map(cat => (
                        <button
                            key={cat}
                            className={classNames('filter-btn', { active: filter === cat })}
                            onClick={() => setFilter(cat)}
                            style={{
                                borderColor: config.cor_header || '#1E1E1E',
                                color: filter === cat ? (config.cor_texto_botoes || '#fff') : (config.cor_header || '#1E1E1E'),
                                backgroundColor: filter === cat ? (config.cor_botoes || '#1E1E1E') : 'transparent'
                            }}
                        >
                            {cat.charAt(0).toUpperCase() + cat.slice(1)}
                        </button>
                    ))}
                </section>
            )}

            <section className="photo-grid">
                {filteredImages.map((img, idx) => (
                    <div
                        key={idx}
                        className="photo-item"
                        onClick={() => setSelectedPhoto(img.url_imagem)}
                    >
                        <img src={img.url_imagem} alt={`Foto da galeria, categoria ${img.categoria}`} loading="lazy" />
                    </div>
                ))}
            </section>

            <div className={classNames('photo-modal-overlay', { 'is-visible': !!selectedPhoto })} onClick={() => setSelectedPhoto(null)}>
                <div className="photo-modal-content" onClick={e => e.stopPropagation()}>
                    <button className="modal-close-btn" onClick={() => setSelectedPhoto(null)}>
                        <X size={32} />
                    </button>
                    {selectedPhoto && <img src={selectedPhoto} alt="Foto ampliada" />}
                </div>
            </div>
        </div>
    );
};

export default Galeria;
