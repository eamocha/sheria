import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCasePageHeader from './LitigationCasePageHeader';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCasePageHeader />, div);
  ReactDOM.unmountComponentAtNode(div);
});