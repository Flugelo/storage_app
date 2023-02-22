import React, {useContext, useEffect, useState} from 'react';
import {AuthContext} from "../Context/AuthContext";
import axios from "axios";
import TableUsers from "./TableUsers";

function Dashboard() {
    const {getToken} = useContext(AuthContext);

    return(
        <div>
           <TableUsers/>
        </div>
    );
}

export default Dashboard;