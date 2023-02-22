import React, {useContext, useEffect, useState} from 'react';
import axios from "axios";
import {AuthContext} from "../Context/AuthContext";

function TableUsers() {
    const {getToken} = useContext(AuthContext);
    const [users, setUsers] = useState([]);

    useEffect(() =>{
        fetchUsers()
    }, [])

    const  fetchUsers = () =>{
        axios.get('api/users', {headers: {Authorization : `Bearer ${getToken}`}}).then(function (response) {
            console.log(response)
            setUsers(response.data);
        }).catch(function (error) {
            console.log(error);
        })
    }

    return (
        <table className='table'>
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            {users.map((user) => (
                <tr key={user.id}>
                    <td>{user.id}</td>
                    <td>{user.name}</td>
                    <td>{user.email}</td>
                    <td>
                        <button className='btn btn-primary'>Editar</button>
                        <button className='btn btn-danger'>Deletar</button>
                    </td>
                </tr>
            ))}
            </tbody>
        </table>
    );
}

export default TableUsers;