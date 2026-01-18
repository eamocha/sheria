import React from 'react';
import ReactDOM from 'react-dom';
import CorporateMatterPage from './CorporateMatterPage';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<CorporateMatterPage />, div);
  ReactDOM.unmountComponentAtNode(div);
});