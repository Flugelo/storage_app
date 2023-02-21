import React, { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';


function Main() {
    return (
        <div>
            <h2>Teste</h2>
        </div>
    );
}

export default Main;

if (document.getElementById('root')) {
    const rootElement = document.getElementById("root");
    const root = createRoot(rootElement);

    root.render(
        <StrictMode>
            <Main />
        </StrictMode>
    );
}