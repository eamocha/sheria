import React from 'react';
import ReactDOM from 'react-dom';
import ImportForm from './ImportForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<ImportForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});