import React from 'react';
import ReactDOM from 'react-dom';
import StageStatus from './StageStatus';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<StageStatus />, div);
  ReactDOM.unmountComponentAtNode(div);
});