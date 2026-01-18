import React from 'react';
import ReactDOM from 'react-dom';
import ResetPasswordForm from './ResetPasswordForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<ResetPasswordForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});