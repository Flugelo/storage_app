import React, {Component, useContext, useEffect, useState} from 'react';
import {AuthContext} from "../Context/AuthContext";
import axios from "axios";
import {useNavigate} from "react-router-dom";

function Login() {
    const [username, setUserName] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState(null);
    const {setAuthToken, getToken} = useContext(AuthContext);
    const navigate = useNavigate();

    useEffect(() => {
        async function isValidToken() {
            const response = await axios.get('/api/token_isvalid', {headers: {Authorization: `Bearer ${getToken}`}});
            axios.defaults.headers.common['accept'] = 'application/json';
            if (response.status === 200) return true; else return false;
        }

        if (getToken !== null) {
            console.log("Não está vasio");
            if(isValidToken()){
                navigate('/home')
            }
        }


    })
    const handleSubmit = async (event) => {
        event.preventDefault();
        try {

            const response = await axios.post('/api/login_check', {
                username: username,
                password: password
            });

            axios.defaults.headers.common['accept'] = 'application/json';
            setAuthToken(response.data.token);
            return navigate("/home");

        } catch (error) {
            setError(error)
            console.error(error);
        }

    }
    return (
        <div className='row justify-content-center'>
            <div className='col-md-6'>
                <div className='card'>
                    <div className='card-header'>
                        <p>Login - Page</p>
                        {error &&

                            <p className='text-danger'>Email ou senha incorretos</p>
                        }
                    </div>
                    <form onSubmit={handleSubmit}>
                        <div className='card-body'>
                            <div className='row-cols-md-6'>
                                <label>Email</label>
                                <input type='text' className='form-control' name='email'
                                       onChange={(event) => setUserName(event.target.value)}/>
                            </div>
                            <div className='row-cols-md-6'>
                                <label>Senha</label>
                                <input type='password' className='form-control' name='password'
                                       onChange={(event) => setPassword(event.target.value)}/>
                            </div>
                        </div>
                        <div className='card-footer'>
                            <div className='row'>
                                <div className='col-md-3'>
                                    <button className='btn btn-primary' type='submit'>Logar</button>
                                </div>
                                <div className='col-md-3'>
                                    <button className='btn btn-danger' type='submit'>Registrar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    )
}

export default Login;