import React, {createContext, useState} from "react";

export const AuthContext = createContext();

const AuthContextProvider = ({children}) => {
    const [token, setToken] = useState(localStorage.getItem('token') || '');

    const setAuthToken = (token) => {
        localStorage.setItem('token', token);
        setToken(token);
    };

    const removeAuthToken = () => {
        localStorage.removeItem('token');
        setToken(null);
    };

    const getToken = () => {
        return token || localStorage.getItem('token');
    };

    return (
        <AuthContext.Provider value={{setAuthToken, removeAuthToken, getToken: getToken()}}>
            {children}
        </AuthContext.Provider>
    );
};

export default AuthContextProvider;