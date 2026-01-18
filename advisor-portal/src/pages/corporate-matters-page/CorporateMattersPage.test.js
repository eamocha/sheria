import React from 'react';
import ReactDOM from 'react-dom';
import CorporateMattersPage from './CorporateMattersPage';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<CorporateMattersPage />, div);
  ReactDOM.unmountComponentAtNode(div);
});