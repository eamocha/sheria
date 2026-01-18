import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCasePageHeaderNav from './LitigationCasePageHeaderNav';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCasePageHeaderNav />, div);
  ReactDOM.unmountComponentAtNode(div);
});