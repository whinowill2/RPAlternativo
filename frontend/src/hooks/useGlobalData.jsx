import { createContext, useContext, useEffect, useState } from 'react';

const DataContext = createContext(null);

export const DataProvider = ({ children }) => {
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const CACHE_KEY = 'rpa_global_data_v7';
                const CACHE_TIME_KEY = 'rpa_global_data_timestamp';
                const cacheExpTime = 60 * 60 * 1000;

                const cachedData = localStorage.getItem(CACHE_KEY);
                const cacheTimestamp = localStorage.getItem(CACHE_TIME_KEY);

                if (cachedData && cacheTimestamp) {
                    const now = new Date().getTime();
                    if (now - parseInt(cacheTimestamp, 10) < cacheExpTime) {
                        try {
                            const parsedData = JSON.parse(cachedData);
                            setData(parsedData);
                            setLoading(false);
                            return;
                        } catch (e) {
                            console.warn("Error parsing cache, fetching fresh.", e);
                        }
                    }
                }

                const api_key = import.meta.env.VITE_API_KEY || 'SUA_API_KEY_AQUI';
                const baseUrl = import.meta.env.VITE_API_URL || '/api';
                const endpoint_url = `${baseUrl}/endpoint.php?action=get_all_data&api_key=${api_key}`;
                const response = await fetch(endpoint_url);
                if (!response.ok) throw new Error('Failed to fetch data');
                const json = await response.json();

                if (json.data && json.data.edicoes) {
                    const meses = { 'janeiro': 1, 'fevereiro': 2, 'março': 3, 'abril': 4, 'maio': 5, 'junho': 6, 'julho': 7, 'agosto': 8, 'setembro': 9, 'outubro': 10, 'novembro': 11, 'dezembro': 12 };

                    json.data.edicoes.sort((a, b) => {
                        let parseDate = (dateStr) => {
                            let val = 0;
                            let lower = dateStr.toLowerCase();
                            if (lower.includes('semestre')) {
                                const yearMatches = lower.match(/(\d{4})/);
                                let ano = yearMatches ? yearMatches[0] : 0;
                                let semestre = lower.includes('1º') ? 1 : 2;
                                val = parseInt(ano + (semestre === 1 ? '06' : '12'));
                            } else if (lower.includes(' de ')) {
                                const parts = lower.split(' de ');
                                let mes = meses[parts[0]] || 0;
                                val = parseInt(parts[1] + String(mes).padStart(2, '0'));
                            }
                            return val;
                        }
                        return parseDate(b.data_lancamento) - parseDate(a.data_lancamento);
                    });
                }

                localStorage.setItem(CACHE_KEY, JSON.stringify(json.data));
                localStorage.setItem(CACHE_TIME_KEY, new Date().getTime().toString());

                setData(json.data);
            } catch (err) {
                setError(err.message);
            } finally {
                setLoading(false);
            }
        };

        fetchData();
    }, []);

    return (
        <DataContext.Provider value={{ data, loading, error }}>
            {children}
        </DataContext.Provider>
    );
};

export const useGlobalData = () => useContext(DataContext);
