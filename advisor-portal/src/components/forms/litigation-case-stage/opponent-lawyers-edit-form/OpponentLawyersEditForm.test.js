import React from 'react';
import ReactDOM from 'react-dom';
import OpponentLawyersEditForm from './OpponentLawyersEditForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<OpponentLawyersEditForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});