import React from 'react';
import ReactDOM from 'react-dom';
import StageDetailsTable from './StageDetailsTable';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<StageDetailsTable />, div);
  ReactDOM.unmountComponentAtNode(div);
});