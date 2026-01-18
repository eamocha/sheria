import React from 'react';
import ReactDOM from 'react-dom';
import OpponentJudges from './OpponentJudges';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<OpponentJudges />, div);
  ReactDOM.unmountComponentAtNode(div);
});