import React from 'react';
import ReactDOM from 'react-dom';
import ActivityHearingsTable from './ActivityHearingsTable';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<ActivityHearingsTable />, div);
  ReactDOM.unmountComponentAtNode(div);
});