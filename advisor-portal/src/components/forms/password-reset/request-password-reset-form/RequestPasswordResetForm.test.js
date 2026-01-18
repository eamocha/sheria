import React from 'react';
import ReactDOM from 'react-dom';
import RequestPasswordResetForm from './RequestPasswordResetForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<RequestPasswordResetForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});