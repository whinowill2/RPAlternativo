import { useState, useEffect } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { Search, Menu, X, ChevronDown } from 'lucide-react';
import { useGlobalData } from '../hooks/useGlobalData';
import classNames from 'classnames';
import '../styles/header.css';

const Header = () => {
    const { data } = useGlobalData();
    const config = data?.configuracoes || {};
    const edicoes = data?.edicoes || [];

    const [scrolled, setScrolled] = useState(false);
    const [menuOpen, setMenuOpen] = useState(false);
    const [searchQuery, setSearchQuery] = useState('');
    const [searchResults, setSearchResults] = useState([]);
    const [isMobileSearchOpen, setIsMobileSearchOpen] = useState(false);

    const navigate = useNavigate();
    const location = useLocation();

    const recentEditions = Array.isArray(edicoes) ? edicoes.slice(1, 5) : [];

    useEffect(() => {
        const handleScroll = () => {
            setScrolled(window.scrollY > 50);
        };
        window.addEventListener('scroll', handleScroll);
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    useEffect(() => {
        if (menuOpen) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = 'unset';
        }
    }, [menuOpen]);

    useEffect(() => {
        setMenuOpen(false);
        setSearchQuery('');
        setSearchResults([]);
    }, [location.pathname]);

    useEffect(() => {
        if (!searchQuery.trim()) {
            setSearchResults([]);
            return;
        }

        const text = searchQuery.toLowerCase();
        const results = edicoes.filter(ed =>
            ed.numero_edicao.toLowerCase().includes(text) ||
            (ed.resumo && ed.resumo.toLowerCase().includes(text))
        ).slice(0, 5);

        setSearchResults(results);
    }, [searchQuery, edicoes]);

    const handleSearch = (e) => {
        e.preventDefault();
        if (searchResults.length > 0) {
            handleResultClick(searchResults[0]);
        }
    };

    const handleResultClick = (edicao) => {
        setSearchQuery('');
        setSearchResults([]);
        navigate(`/edicoes/edicao/${edicao.numero_edicao.replace('Edição #', '').trim()}`);
    };

    return (
        <>
            <header
                className={classNames('site-header', { scrolled })}
                style={{
                    backgroundColor: scrolled ? (config.cor_header || 'rgba(30,30,30,0.9)') : 'transparent',
                }}
            >
                <div className="mobile-menu-toggle" onClick={() => setMenuOpen(true)}>
                    <Menu size={28} color="#e3e3e3" />
                </div>

                <Link to="/" className="header-logo-container">
                    <img className="logo-rpa" src={config.site_logo} alt="Logotipo da Revista RPA" />
                    <img className="logo-rpa-mobile" src={config.site_logo_mobile} alt="Logotipo da Revista RPA" />
                </Link>

                <div className="mobile-search-toggle" onClick={() => setIsMobileSearchOpen(true)}>
                    <Search size={24} color="white" />
                </div>

                <nav className="nav-header">
                    <ul>
                        <li className="nav"><Link to="/sobre">Sobre a Revista</Link></li>

                        <li className="has-dropdown nav">
                            <a href="#">
                                Edições Anteriores <ChevronDown size={16} />
                            </a>
                            <ul className="dropdown-menu">
                                {recentEditions.map((ed, idx) => (
                                    <li key={idx}>
                                        <Link style={{ color: 'black' }} to={`/edicoes/edicao/${ed.numero_edicao.replace('Edição #', '').trim()}`}>
                                            {ed.numero_edicao}
                                        </Link>
                                    </li>
                                ))}
                                <li><Link style={{ color: 'black' }} to="/edicoes">Ver todas</Link></li>
                            </ul>
                        </li>

                        <li className="nav"><Link to="/expediente">Expediente {edicoes[0] ? edicoes[0].numero_edicao.replace('Edição ', '') : 'Última Edição'}</Link></li>
                        <li className="nav"><Link to="/galeria">Galeria</Link></li>
                        <li className="nav search-container" style={{ position: 'relative' }}>
                            <form onSubmit={handleSearch} className="search-form">
                                <Search size={22} color="white" className="search-icon" />
                                <input
                                    type="text"
                                    placeholder="Buscar edições..."
                                    className="search-input"
                                    value={searchQuery}
                                    onChange={(e) => setSearchQuery(e.target.value)}
                                />
                            </form>
                            {searchResults.length > 0 && (
                                <ul className="search-results-dropdown">
                                    {searchResults.map((res, idx) => (
                                        <li key={idx} onClick={() => handleResultClick(res)}>
                                            <img src={res.url_capa} alt={res.numero_edicao} />
                                            <div>
                                                <strong>{res.numero_edicao}</strong>
                                                <span>{res.data_lancamento}</span>
                                            </div>
                                        </li>
                                    ))}
                                </ul>
                            )}
                        </li>
                        <li className="nav">
                            <a href="#" target="_blank" rel="noopener noreferrer">
                                <img className="logo-ufma" src="https://placehold.co/100x40?text=Sua+Logo" alt="Logo Parceiro" />
                            </a>
                        </li>


                        <li className="menu" onClick={() => setMenuOpen(true)}>
                            <a href="#"><Menu size={24} color="#e3e3e3" /></a>
                        </li>
                    </ul>
                </nav>
            </header>

            <div className={classNames('menu-overlay', { 'is-open': menuOpen })} onClick={() => setMenuOpen(false)}></div>
            <nav className={classNames('side-menu', { 'is-open': menuOpen })}>
                <div className="side-menu-header">
                    <Link to="/" onClick={() => setMenuOpen(false)}>
                        <img style={{ width: '100px', height: 'auto' }} src="https://placehold.co/200x100?text=Logo" alt="Logotipo Principal" />
                    </Link>
                    <button className="close-menu-btn" onClick={() => setMenuOpen(false)}>
                        <X size={24} />
                    </button>
                </div>
                <ul className="side-menu-links">
                    <li><Link to="/sobre" onClick={() => setMenuOpen(false)}>Sobre a Revista</Link></li>
                    <li><Link to="/edicoes" onClick={() => setMenuOpen(false)}>Todas as edições</Link></li>
                    <li><Link to="/expediente" onClick={() => setMenuOpen(false)}>Expediente</Link></li>
                    <li><Link to="/galeria" onClick={() => setMenuOpen(false)}>Galeria</Link></li>
                    <li><a href="#" target="_blank" rel="noopener noreferrer">Seu Link</a></li>
                    <li><a href="mailto:seuemail@dominio.com.br">Fale Conosco</a></li>
                </ul>
            </nav>

            <div className={classNames('mobile-search-modal', { 'is-open': isMobileSearchOpen })}>
                <div className="mobile-search-header">
                    <input
                        type="text"
                        placeholder="Buscar edições..."
                        value={searchQuery}
                        onChange={(e) => setSearchQuery(e.target.value)}
                        autoFocus={isMobileSearchOpen}
                    />
                    <button onClick={() => { setIsMobileSearchOpen(false); setSearchQuery(''); }}><X size={24} color="#1E1E1E" /></button>
                </div>
                {searchResults.length > 0 && (
                    <div className="mobile-search-results">
                        {searchResults.map((res, idx) => (
                            <div key={idx} className="search-result-item" onClick={() => {
                                setIsMobileSearchOpen(false);
                                handleResultClick(res);
                            }}>
                                <img src={res.url_capa} alt={res.numero_edicao} />
                                <div>
                                    <strong>{res.numero_edicao}</strong>
                                    <span>{res.data_lancamento}</span>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            <style>{`
        .search-container {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            position: relative;
            width: 40px;
            height: 40px;
        }
        .search-form {
            position: absolute;
            right: 0;
            display: flex;
            align-items: center;
            border-radius: 50px;
            padding: 5px;
            transition: all 0.4s ease;
            width: 40px; 
            overflow: visible;
            border: 1px solid transparent;
            z-index: 10;
        }
        .search-form:hover, .search-form:focus-within {
            width: 200px;
            background: rgba(20, 20, 20, 0.9);
            border: 1px solid rgba(255,255,255,0.3);
        }
        .search-icon {
            min-width: 22px;
            margin-left: 4px;
            cursor: pointer;
            z-index: 11;
        }
        .search-input {
            background: transparent;
            border: none;
            color: white;
            outline: none;
            padding: 0 10px;
            width: 100%;
            font-family: inherit;
            opacity: 0;
            transition: opacity 0.4s ease;
        }
        .search-form:hover .search-input, .search-input:focus {
            opacity: 1;
        }
        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        .search-results-dropdown {
                position: absolute;
    display: flex;
    top: 150%;
    right: 0;
    background: white;
    border-radius: var(--radius);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    width: 260px;
    padding: 30px !important;
    list-style: none;
    padding: 10px;
    margin: 0;
    z-index: 100;
    flex-direction: column;
    align-items: flex-start !important;
        }
        .search-results-dropdown li {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px;
            cursor: pointer;
            border-radius: 8px;
            transition: background 0.2s ease;
        }
        .search-results-dropdown li:hover {
            background: var(--cor-light-gray);
        }
        .search-results-dropdown img {
            width: 40px;
            height: 56px;
            object-fit: cover;
            border-radius: 4px;
        }
        .search-results-dropdown div {
            display: flex;
            flex-direction: column;
            color: var(--cor-dark);
        }
        .search-results-dropdown strong {
            font-size: 14px;
            color: var(--cor-laranja);
        }
        .search-results-dropdown span {
            font-size: 12px;
            color: var(--cor-text-secondary);
        }
      `}</style>
        </>
    );
};

export default Header;
