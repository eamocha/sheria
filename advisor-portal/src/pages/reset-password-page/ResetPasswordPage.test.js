import React from 'react';
import ReactDOM from 'react-dom';
import ResetPasswordPage from './ResetPasswordPage';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<ResetPasswordPage />, div);
  ReactDOM.unmountComponentAtNode(div);
});