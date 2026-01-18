import React from 'react';
import ReactDOM from 'react-dom';
import TableRowDetailsItem from './TableRowDetailsItem';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<TableRowDetailsItem />, div);
  ReactDOM.unmountComponentAtNode(div);
});