import React from 'react';
import ReactDOM from 'react-dom';
import ActivityHearingsTableHead from './ActivityHearingsTableHead';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<ActivityHearingsTableHead />, div);
  ReactDOM.unmountComponentAtNode(div);
});