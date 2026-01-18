import React from 'react';
import ReactDOM from 'react-dom';
import OpponentJudgesEditForm from './OpponentJudgesEditForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<OpponentJudgesEditForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});