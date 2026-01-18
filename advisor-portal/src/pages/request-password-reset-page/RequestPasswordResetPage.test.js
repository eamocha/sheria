import React from 'react';
import ReactDOM from 'react-dom';
import RequestPasswordResetPage from './RequestPasswordResetPage';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<RequestPasswordResetPage />, div);
  ReactDOM.unmountComponentAtNode(div);
});