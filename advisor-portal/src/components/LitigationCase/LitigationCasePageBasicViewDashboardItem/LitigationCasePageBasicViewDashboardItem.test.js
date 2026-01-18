import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCasePageBasicViewDashboardItem from './LitigationCasePageBasicViewDashboardItem';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCasePageBasicViewDashboardItem />, div);
  ReactDOM.unmountComponentAtNode(div);
});