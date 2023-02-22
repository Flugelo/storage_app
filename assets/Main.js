import React, {StrictMode} from 'react';
import {createRoot} from 'react-dom/client';
import {BrowserRouter, Route, Routes} from "react-router-dom";

//Components
import Login from './pages/Login';

//Context
import AuthProvider from "./Context/AuthContext";


//Pages
import Home from "./pages/home/home";

function Main() {

    return (
        <AuthProvider>
                <BrowserRouter>
                    <Routes>
                        <Route exact path="/" element={<Login/>}/>
                        <Route exact path="/login" element={<Login/>}/>
                        <Route path="/home" element={<Home/>}/>
                    </Routes>
                </BrowserRouter>
        </AuthProvider>
    );
}

export default Main;

if (document.getElementById('root')) {
    const rootElement = document.getElementById("root");
    const root = createRoot(rootElement);

    root.render(
        <StrictMode>
            <Main/>
        </StrictMode>
    );
}