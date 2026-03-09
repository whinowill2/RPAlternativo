import { Outlet, useLocation } from 'react-router-dom';
import Header from '../components/Header';
import Footer from '../components/Footer';
import TabBar from '../components/TabBar';
import SecondaryTabBar from '../components/SecondaryTabBar';
import { useGlobalData } from '../hooks/useGlobalData';

const MainLayout = () => {
    const location = useLocation();
    const { data } = useGlobalData();
    const edicoes = data?.edicoes || [];
    const edicaoMaisRecente = edicoes[0] || null;
    const numero_recente = edicaoMaisRecente ? edicaoMaisRecente.numero_edicao.replace('Edição #', '').trim() : '';
    const isCurrentEditionPage = numero_recente ? location.pathname === `/edicoes/edicao/${numero_recente}` : false;

    const isSecondaryTab =
        location.pathname.startsWith('/sobre') ||
        location.pathname.startsWith('/expediente') ||
        (location.pathname.startsWith('/edicoes/edicao/') && !isCurrentEditionPage);

    return (
        <>
            <Header />
            <main className="main" style={{ minHeight: 'calc(100vh - 300px)' }}>
                <Outlet />
            </main>
            <Footer />
            {isSecondaryTab ? <SecondaryTabBar /> : <TabBar />}
        </>
    );
};

export default MainLayout;
