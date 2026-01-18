import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCasesPage from './LitigationCasesPage';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCasesPage />, div);
  ReactDOM.unmountComponentAtNode(div);
});