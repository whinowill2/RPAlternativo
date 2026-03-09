import { useGlobalData } from '../hooks/useGlobalData';
import { Link } from 'react-router-dom';
import { Swiper, SwiperSlide } from 'swiper/react';
import { Navigation, A11y } from 'swiper/modules';
import { ChevronRight, ArrowRight, ArrowLeft } from 'lucide-react';
import Button from '../components/Button';
import { Helmet } from 'react-helmet-async';
import 'swiper/css';
import 'swiper/css/navigation';
import '../styles/home.css';

const Home = () => {
    const { data, loading, error } = useGlobalData();

    if (loading) return <div className="loader">Carregando...</div>;
    if (error) return <div className="error">Erro ao carregar dados: {error}</div>;

    const config = data?.configuracoes || {};
    const inical = data?.pagina_inicial || {};
    const edicoes = data?.edicoes || [];
    const expediente = data?.expediente || [];

    const edicaoMaisRecente = edicoes[0] || null;
    const numero_recente = edicaoMaisRecente ? edicaoMaisRecente.numero_edicao.replace('Edição #', '').trim() : '';
    const url_recente = `/edicoes/edicao/${numero_recente}`;

    return (
        <>
            <Helmet>
                <title>Nome do Projeto | Seu Slogan ou Descrição Curta Aqui</title>
                <meta name="description" content="Descrição detalhada sobre o seu projeto ou publicação. Adicione aqui informações sobre a equipe, propósito e frequência de atualização." />
                <meta property="og:title" content="Nome do Projeto | Seu Slogan ou Descrição Curta Aqui" />
                <meta property="og:description" content="Descrição detalhada sobre o seu projeto ou publicação. Adicione aqui informações sobre a equipe, propósito e frequência de atualização." />
                <meta property="og:image" content="https://placehold.co/1200x630?text=Imagem+de+Compartilhamento"></meta>
            </Helmet>
            <section
                className="hero-section"
                style={{
                    backgroundColor: config.cor_principal || '#ff6b00',
                    backgroundImage: `linear-gradient(rgba(30,30,30,0.9), rgba(30,30,30,0.9)), url(${inical.hero_image_url || ''})`,
                    backdropFilter: 'blur(12px)',
                    WebkitBackdropFilter: 'blur(12px)'
                }}
            >
                <div className="hero-text">
                    <h1>{inical.hero_title}</h1>
                    <p dangerouslySetInnerHTML={{ __html: inical.sobre_p1?.replace(/\n/g, '<br />') }} />
                    <Button to={url_recente} variant="light" className="active-scale" icon={ChevronRight}>
                        Ler agora
                    </Button>
                </div>
                <div className="hero-image">
                    <img src={inical.hero_cover_url || "https://placehold.co/600x800?text=Capa+da+Revista"} alt="Capa da Revista" />
                </div>
            </section>

            <section className="section sobre-section" style={{ backgroundColor: 'var(--cor-dark)' }}>
                <div className="container">
                    <h2 className="section-title" style={{ color: '#ffffffff' }}>Sobre a Revista</h2>
                    <p style={{ color: '#ffffffff', fontSize: '0.8rem' }} dangerouslySetInnerHTML={{ __html: inical.sobre_p2?.replace(/\n/g, '<br />') }} />
                    <Button
                        to="/sobre"
                        variant="light"
                        className="hover-scale"
                        icon={ArrowRight}
                        style={{ marginTop: '30px' }}
                    >
                        Ler história completa
                    </Button>
                </div>
            </section>

            <section className="section edicoes-section" style={{ backgroundColor: 'white', marginTop: '-5px', zIndex: 2 }}>
                <div className="container" style={{ position: 'relative' }}>
                    <h2 className="section-title text-dark" style={{ paddingLeft: '6%' }}>Edições anteriores</h2>

                    <div className="swiper-wrapper-edicoes">
                        <Swiper
                            modules={[Navigation, A11y]}
                            spaceBetween={20}
                            slidesPerView={'auto'}
                            slidesOffsetBefore={20}
                            slidesOffsetAfter={80}
                            navigation={{
                                nextEl: '.swiper-btn-next',
                                prevEl: '.swiper-btn-prev',
                            }}
                            breakpoints={{
                                320: { slidesPerView: 1.8, spaceBetween: 8, slidesOffsetBefore: 16, slidesOffsetAfter: 50 },
                                640: { slidesPerView: 2.6, spaceBetween: 14, slidesOffsetBefore: 24, slidesOffsetAfter: 70 },
                                1024: { slidesPerView: 4.2, spaceBetween: 24, slidesOffsetBefore: 30, slidesOffsetAfter: 80 }
                            }}
                        >
                            {edicoes.map((ed, idx) => {
                                const num = ed.numero_edicao.replace('Edição #', '').trim();
                                return (
                                    <SwiperSlide key={idx} className="slide-card">
                                        <img src={ed.url_capa} alt={`Capa da ${ed.numero_edicao}`} />
                                        <h3 className="text-dark">{ed.numero_edicao}</h3>
                                        <Button
                                            to={`/edicoes/edicao/${num}`}
                                            variant="light"
                                            className="premium-btn"
                                            style={{ marginTop: '20px', backgroundColor: config.cor_botoes, color: config.cor_texto_botoes, fontSize: '0.9rem', padding: '8px 16px' }}
                                        >
                                            Ler Revista
                                        </Button>
                                    </SwiperSlide>
                                )
                            })}
                        </Swiper>

                        <button className="slider-arrow swiper-btn-prev" style={{ backgroundColor: 'var(--cor-dark)', color: 'var(--cor-white)' }}><ArrowLeft size={20} /></button>
                        <button className="slider-arrow swiper-btn-next" style={{ backgroundColor: 'var(--cor-dark)', color: 'var(--cor-white)' }}><ArrowRight size={20} /></button>
                    </div>

                    <div style={{ textAlign: 'center', marginTop: '40px' }}>
                        <Button
                            to="/edicoes"
                            variant="light"
                            style={{ backgroundColor: config.cor_botoes, color: config.cor_texto_botoes }}

                        >
                            Ver todas as edições anteriores
                        </Button>
                    </div>
                </div>
            </section>

            <section className="expediente-section section" style={{ backgroundColor: 'var(--cor-dark)' }}>
                <div className="container" style={{ position: 'relative' }}>
                    <h2 className="section-title text-white">Expediente da {edicaoMaisRecente ? edicaoMaisRecente.numero_edicao : 'Última Edição'}</h2>

                    <div className="swiper-wrapper-expediente exp-swiper">
                        <Swiper
                            modules={[Navigation, A11y]}
                            spaceBetween={30}
                            slidesPerView={'auto'}
                            slidesOffsetBefore={20}
                            slidesOffsetAfter={80}
                            navigation={{
                                nextEl: '.swiper-exp-next',
                                prevEl: '.swiper-exp-prev',
                            }}
                            breakpoints={{
                                320: { slidesPerView: 'auto', spaceBetween: 15, slidesOffsetBefore: 0, slidesOffsetAfter: 60 },
                                640: { slidesPerView: 'auto', spaceBetween: 20, slidesOffsetBefore: 0, slidesOffsetAfter: 80 },
                                1024: { slidesPerView: 'auto', spaceBetween: 30, slidesOffsetBefore: 0, slidesOffsetAfter: 80 }
                            }}
                        >
                            {expediente.map((membro, idx) => (
                                <SwiperSlide key={idx} className="membro-card">
                                    <div className="membro-foto" style={{ backgroundImage: `url(${membro.url_foto})` }}></div>
                                    <h3 className="text-white">{membro.nome.split(' ')[0]}</h3>
                                </SwiperSlide>
                            ))}
                        </Swiper>
                        <button className="slider-arrow swiper-exp-prev" style={{ backgroundColor: 'var(--cor-white)', color: 'var(--cor-dark)' }}><ArrowLeft size={20} /></button>
                        <button className="slider-arrow swiper-exp-next" style={{ backgroundColor: 'var(--cor-white)', color: 'var(--cor-dark)' }}><ArrowRight size={20} /></button>
                    </div>

                    <div style={{ textAlign: 'center', marginTop: '40px', paddingBottom: '40px' }}>
                        <Button
                            to="/expediente"
                            variant="light"
                            style={{ color: config.cor_header || '#1E1E1E', backgroundColor: '#fff' }}
                            icon={ArrowRight}
                        >
                            Ver equipe completa
                        </Button>
                    </div>
                </div>
            </section>
        </>
    );
};

export default Home;
