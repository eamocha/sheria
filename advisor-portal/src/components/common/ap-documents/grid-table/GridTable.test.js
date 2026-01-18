import React from 'react';
import ReactDOM from 'react-dom';
import GridTable from './GridTable';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<GridTable />, div);
  ReactDOM.unmountComponentAtNode(div);
});