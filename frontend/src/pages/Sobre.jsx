import { useGlobalData } from '../hooks/useGlobalData';
import { PenTool, Target, Layers } from 'lucide-react';
import { Helmet } from 'react-helmet-async';
import '../styles/sobre.css';

const Sobre = () => {
    const { data, loading } = useGlobalData();
    const config = data?.configuracoes || {};

    if (loading) {
        return <div className="loading-spinner">Carregando...</div>;
    }

    return (
        <div className="about-page-wrapper">
            <Helmet>
                <title>Sobre | Nome do Projeto</title>
                <meta name="description" content="Conheça a história, missão e valores do Projeto." />
                <meta property="og:title" content="Sobre | Nome do Projeto" />
                <meta property="og:description" content="Conheça a história, missão e valores do Projeto." />
            </Helmet>
            <section className="about-intro-card" style={{ backgroundColor: config.cor_header, color: config.cor_texto_header }}>
                <div className="container">
                    <h1>Sobre o Projeto</h1>
                    <p>Substitua este texto por informações que descrevam o propósito da sua revista, comunidade, ou portal. O texto original tratava de um curso universitário específico, mas sinta-se à vontade para detalhar aqui quem produz o material e com que frequência ele é lançado.</p>
                    <p>Neste segundo parágrafo, detalhe a <strong>missão</strong> de sua iniciativa. Explique os motivos pelos quais ela foi criada e o que espera atingir com cada uma das edições e lançamentos.</p>
                    <p>Por fim, detalhe a <strong>visão</strong> e os <strong>valores</strong> deste portal, para engajar leitores e novos contribuidores.</p>
                </div>
            </section>

            <section className="about-details-card container">
                <div className="detail-item">
                    <div className="icon"><Target size={32} color={config.cor_laranja || "#F37013"} /></div>
                    <div className="text-content">
                        <h3>SITE</h3>
                        <p>Adicione mais contexto sobre como o site ou plataforma digital ajuda a consolidar e organizar as informações ou as publicações.</p>
                        <p>Destaque quem participou da construção desta plataforma e de quem partiu a iniciativa. Reconhecer criadores e mantenedores em projetos open-source é essencial para incentivar contribuições.</p>
                        <p>Explique a importância geral desta vitrine virtual, que servirá tanto como repositório de histórico e aprendizados da comunidade quanto meio de pesquisa para os futuros interessados na área abordada pelo projeto.</p>
                    </div>
                </div>

                <div className="detail-item">
                    <div className="icon"><PenTool size={32} color={config.cor_laranja || "#F37013"} /></div>
                    <div className="text-content">
                        <h3>TEXTOS DAS EDITORIAS CLÁSSICAS</h3>
                        <p>Liste aqui as categorias, tipos de matérias ou formatos padrões do seu projeto, portal ou revista.</p>
                    </div>
                </div>
            </section>
        </div>
    );
};

export default Sobre;
