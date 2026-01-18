import React from 'react';
import ReactDOM from 'react-dom';
import OpponentLawyersForm from './OpponentLawyersForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<OpponentLawyersForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});