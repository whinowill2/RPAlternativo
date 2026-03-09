import { useGlobalData } from '../hooks/useGlobalData';
import { Instagram, Twitter, Mail, Facebook } from 'lucide-react';
import { useState } from 'react';

const Footer = () => {
    const { data } = useGlobalData();
    const config = data?.configuracoes || {};
    const [isIntroActive, setIsIntroActive] = useState(true);

    const handleAnimationEnd = (event) => {
        if (event.animationName === 'rotate-effect-intro' && isIntroActive) {
            setIsIntroActive(false);
        }
    };

    return (
        <footer className="site-footer" style={{ backgroundColor: config.cor_header || '#1E1E1E', padding: '60px 5% 120px', color: '#fff' }}>
            <div className="container">
                <div className="footer-top" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '40px', flexWrap: 'wrap', gap: '20px' }}>
                    <div className="footer-logo">
                        <img className="logo-rpa" src={config.site_logo} alt="Logotipo da Revista RPA" />
                        <img className="logo-rpa-mobile" src={config.site_logo_mobile} alt="Logotipo da Revista RPA" />
                    </div>
                    <div className="footer-socials" style={{ display: 'flex', gap: '15px' }}>
                        <a href="https://instagram.com/seuperfil" target="_blank" rel="noreferrer" aria-label="Instagram">
                            <Instagram size={24} />
                        </a>

                        <a href="mailto:seuemail@dominio.com.br" aria-label="Email">
                            <Mail size={24} />
                        </a>

                    </div>
                </div>

                <div className="footer-bottom">
                    <div className="developed" style={{ marginTop: '20px', fontSize: '1rem' }}>
                        <p>&copy; {new Date().getFullYear()} Nome do Projeto - Todos os direitos reservados<br />
                            Seu Endereço Aqui - CEP: 00000-000</p>

                        <p className="developed">Projeto Open Source desenvolvido por:  <strong> Giovana Garcia, Kerienn Teles, Maria Victória Sousa, Samantha Santos e Will Ribeiro</strong></p>
                    </div>

                </div>
            </div>
        </footer>
    );
};

export default Footer;
