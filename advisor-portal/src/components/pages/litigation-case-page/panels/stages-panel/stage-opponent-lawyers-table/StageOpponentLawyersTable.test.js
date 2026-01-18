import React from 'react';
import ReactDOM from 'react-dom';
import StageOpponentLawyersTable from './StageOpponentLawyersTable';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<StageOpponentLawyersTable />, div);
  ReactDOM.unmountComponentAtNode(div);
});