<!DOCTYPE html>
<html>
    <head>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
    <script type="text/javascript">
        // Set timeout variables.
        var timoutWarning = 60000; // Display warning in 1Mins.
        var timoutNow = 120000; // Timeout in 2 mins.
        var logoutUrl = 'https://www.logicrays.com/';

        var warningTimer;
        var timeoutTimer;

        // Start timers.
        function StartTimers() {
            warningTimer = setTimeout("IdleWarning()", timoutWarning);
            timeoutTimer = setTimeout("IdleTimeout()", timoutNow);
        }

        // Reset timers.
        function ResetTimers() {
            clearTimeout(warningTimer);
            clearTimeout(timeoutTimer);
            StartTimers();
            jQuery(document).ready(function() {
                jQuery("#timeout").dialog('close');
            });
        }

        // Show idle timeout warning dialog.
        function IdleWarning() {
            jQuery(document).ready(function() {
                jQuery("#timeout").dialog({
                    modal: true
                });
            });
        }

        // Logout the user.
        function IdleTimeout() {
            window.location = logoutUrl;
        }

        var counter = 60;
        var interval = setInterval(function() {
            counter--;
            // Display 'counter' wherever you want to display it.
            if (counter <= 0) {
                    clearInterval(interval);
                $('#timer').html("<h3>Count down complete</h3>");  
                return;
            }else{
                $('#time').text(counter);
              console.log("Timer --> " + counter);
            }
        }, 1000);
    </script>
</head>
<body onload="StartTimers();" onmousemove="ResetTimers();">
    <form id="form1" runat="server">
    <div id="timeout">
        <h1>
            Session About To Timeout</h1>
        <p>
            You will be automatically logged out in 1 minute.<br />
        To remain logged in move your mouse over this window.
    </div>
    <table id="table1" align="center" border="1" width="800" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                Hello World
            </td>
        </tr>
    </table>
    <div>
      <span id="timer">
        <span id="time">10</span>Seconds      
      </span>
    </div>
  
    </form>
</body>
</html>