import React from 'react';
import ReactDOM from 'react-dom';
import StageDetailsTableRowItem from './StageDetailsTableRowItem';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<StageDetailsTableRowItem />, div);
  ReactDOM.unmountComponentAtNode(div);
});