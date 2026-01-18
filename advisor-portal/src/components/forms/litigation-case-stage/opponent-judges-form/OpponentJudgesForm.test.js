import React from 'react';
import ReactDOM from 'react-dom';
import OpponentJudgesForm from './OpponentJudgesForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<OpponentJudgesForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});