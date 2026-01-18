import React from 'react';
import ReactDOM from 'react-dom';
import APTextFieldInput from './APTextFieldInput';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APTextFieldInput />, div);
  ReactDOM.unmountComponentAtNode(div);
});