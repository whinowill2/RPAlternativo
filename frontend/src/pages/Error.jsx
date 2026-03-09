import React from 'react';
import { useLocation, useParams } from 'react-router-dom';
import Button from '../components/Button';
import { Home } from 'lucide-react';

const errorMessages = {
    400: "Ooops! Você fez um pedido que nem a gente entendeu.",
    401: "Alto lá! Você não tem crachá para entrar aqui.",
    403: "Acesso Negado. A Samantha não ia querer que você entrasse aqui.",
    404: "Ih, parece que o desenvolvedor esqueceu de criar essa página!",
    500: "Opa! Alguém tropeçou nos fios do servidor. Já estamos resolvendo!",
    502: "Bad Gateway... ou seria 'Portão Quebrado'? O servidor demorou muito.",
    503: "Serviço indisponível. Estamos no horário do café.",
    default: "Ocorreu um erro misterioso no universo."
};

const ErrorPage = ({ code: propCode }) => {
    const location = useLocation();
    const { code: paramCode } = useParams();

    const rawCode = propCode || paramCode || location.state?.errorCode || 404;
    const errorCode = parseInt(rawCode, 10) || 404;
    const message = errorMessages[errorCode] || errorMessages.default;

    return (
        <section className="section" style={{ backgroundColor: 'var(--cor-dark)', minHeight: '60vh', display: 'flex', flexDirection: 'column', justifyContent: 'center', alignItems: 'center', textAlign: 'center' }}>
            <h1 style={{ fontSize: '7rem', color: 'var(--cor-laranja)', margin: '100px 0 20px 0', lineHeight: 1 }}>{errorCode}</h1>
            <h2 style={{ fontSize: '2rem', marginBottom: '20px', color: 'var(--cor-white)' }}>{message}</h2>
            <p style={{ marginBottom: '40px', color: '#ccc' }}>
                Que tal tentar novamente mais tarde?
            </p>
            <Button to="/" variant="light" icon={Home}>
                Voltar para o Início
            </Button>
        </section>
    );
};

export default ErrorPage;
