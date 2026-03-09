import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { DataProvider } from './hooks/useGlobalData';
import MainLayout from './layouts/MainLayout';
import Home from './pages/Home';
import Sobre from './pages/Sobre';
import Edicoes from './pages/Edicoes';
import EdicaoInterna from './pages/EdicaoInterna';
import Expediente from './pages/Expediente';
import Galeria from './pages/Galeria';
import ErrorPage from './pages/Error';

function App() {
    return (
        <DataProvider>
            <BrowserRouter>
                <Routes>
                    <Route path="/" element={<MainLayout />}>
                        <Route index element={<Home />} />
                        <Route path="sobre" element={<Sobre />} />
                        <Route path="edicoes">
                            <Route index element={<Edicoes />} />
                            <Route path="edicao/:id" element={<EdicaoInterna />} />
                        </Route>
                        <Route path="expediente" element={<Expediente />} />
                        <Route path="galeria" element={<Galeria />} />
                        <Route path="erro/:code" element={<ErrorPage />} />
                        <Route path="*" element={<ErrorPage />} />
                    </Route>
                </Routes>
            </BrowserRouter>
        </DataProvider>
    );
}

export default App;
