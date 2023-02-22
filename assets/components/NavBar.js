import React, {Component} from 'react';
import Container from 'react-bootstrap/Container';
import {Button, Form, InputGroup, Nav, Navbar, NavDropdown} from "react-bootstrap";

function NavBar(props) {
    return (
        <Navbar bg="light" expand="lg">
            <Container className='m-0'>
                <Navbar.Brand href="#home">Logo</Navbar.Brand>
                <Navbar.Toggle aria-controls="basic-navbar-nav"/>
                <Navbar.Collapse id="basic-navbar-nav">
                    <Nav className="me-auto">
                        <Nav.Link href="/home">Home</Nav.Link>
                        <NavDropdown title='Cadastros' id='basic-nav-cadastros'>
                            <NavDropdown.Item href='#'>Produto</NavDropdown.Item>
                            <NavDropdown.Item href='#'>Venda</NavDropdown.Item>
                            <NavDropdown.Divider/>
                            <NavDropdown.Item href='#'>Fornecedor</NavDropdown.Item>
                            <NavDropdown.Divider/>
                            <NavDropdown.Item href='#'>Usuário</NavDropdown.Item>
                        </NavDropdown>
                        <NavDropdown title="Produtos" id="basic-nav-dropdown">
                            <NavDropdown.Item href="#action/3.1">Vendidos</NavDropdown.Item>
                            <NavDropdown.Item href="#action/3.2">Estoque</NavDropdown.Item>
                        </NavDropdown>
                        <Nav.Link href='#'>Usuários</Nav.Link>
                    </Nav>
                    <form className='form-check-inline my-2 my-lg-0'>
                        <InputGroup>
                            <Form.Control placeholder='Pesquisar' aria-label="Pesquisar" aria-describedby="casic-addon2"/>
                            <Button variant="outline-success" id="button-addon2">Pesquisar</Button>
                        </InputGroup>
                    </form>
                </Navbar.Collapse>
            </Container>
        </Navbar>
    );
}

export default NavBar;