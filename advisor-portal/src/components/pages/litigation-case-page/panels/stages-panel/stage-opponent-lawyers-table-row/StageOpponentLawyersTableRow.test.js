import React from 'react';
import ReactDOM from 'react-dom';
import StageOpponentLawyersTableRow from './StageOpponentLawyersTableRow';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<StageOpponentLawyersTableRow />, div);
  ReactDOM.unmountComponentAtNode(div);
});