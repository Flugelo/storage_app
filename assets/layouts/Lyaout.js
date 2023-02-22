import React, {useContext, useEffect} from 'react';
import NavBar from "../components/NavBar";
import {useNavigate} from "react-router-dom";


const Layout = ({children}) => {

    return (
        <>
            <NavBar/>
            <div className="section">
                {children}
            </div>
        </>
    )
}

export default Layout;