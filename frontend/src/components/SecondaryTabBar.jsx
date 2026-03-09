import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import classNames from 'classnames';
import { ArrowLeft, Share2 } from 'lucide-react';
import '../styles/tabbar.css';

const SecondaryTabBar = () => {
    const [visible, setVisible] = useState(false);
    const navigate = useNavigate();

    useEffect(() => {
        const timer = setTimeout(() => setVisible(true), 500);
        return () => clearTimeout(timer);
    }, []);

    const handleBack = () => {
        navigate(-1);
    };

    const handleShare = async () => {
        try {
            if (navigator.share) {
                await navigator.share({
                    title: document.title,
                    text: `Olha que incrível o que eu achei: ${window.location.href}`,
                    url: window.location.href,
                });
            } else {
                await navigator.clipboard.writeText(window.location.href);
                alert("Link copiado para a área de transferência!");
            }
        } catch (err) {
            console.error("Erro ao compartilhar:", err);
        }
    };

    return (
        <nav className={classNames('mobile-tab-bar-2', { visible })}>
            <button
                onClick={handleBack}
                className="tab-item-2 tab-item-horizontal tab-button-reset"
            >
                <ArrowLeft size={18} />
                <span>Voltar</span>
            </button>
            <button
                onClick={handleShare}
                className="tab-item-2 tab-item-horizontal tab-button-reset"
            >
                <Share2 size={18} />
                <span>Compartilhar</span>
            </button>
        </nav>
    );
};

export default SecondaryTabBar;
