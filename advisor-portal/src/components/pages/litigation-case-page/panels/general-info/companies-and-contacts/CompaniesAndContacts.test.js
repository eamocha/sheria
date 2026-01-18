import React from 'react';
import ReactDOM from 'react-dom';
import CompaniesAndContacts from './CompaniesAndContacts';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<CompaniesAndContacts />, div);
  ReactDOM.unmountComponentAtNode(div);
});