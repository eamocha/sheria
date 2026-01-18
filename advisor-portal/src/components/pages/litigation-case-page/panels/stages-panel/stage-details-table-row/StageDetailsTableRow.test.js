import React from 'react';
import ReactDOM from 'react-dom';
import StageDetailsTableRow from './StageDetailsTableRow';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<StageDetailsTableRow />, div);
  ReactDOM.unmountComponentAtNode(div);
});