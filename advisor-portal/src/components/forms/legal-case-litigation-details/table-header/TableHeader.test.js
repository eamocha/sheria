import React from 'react';
import ReactDOM from 'react-dom';
import TableHeader from './TableHeader';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<TableHeader />, div);
  ReactDOM.unmountComponentAtNode(div);
});