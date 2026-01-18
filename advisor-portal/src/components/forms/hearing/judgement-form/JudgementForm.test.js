import React from 'react';
import ReactDOM from 'react-dom';
import JudgementForm from './JudgementForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<Judgementorm />, div);
  ReactDOM.unmountComponentAtNode(div);
});