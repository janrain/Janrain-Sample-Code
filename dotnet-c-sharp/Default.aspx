<%@ Page Title="Home Page" Language="C#" MasterPageFile="~/Site.master" AutoEventWireup="true"
    CodeFile="Default.aspx.cs" Inherits="_Default" %>

<asp:Content ID="HeaderContent" runat="server" ContentPlaceHolderID="HeadContent"></asp:Content>
<asp:Content ID="BodyContent" runat="server" ContentPlaceHolderID="MainContent">
   
<a class="rpxnow" onclick="return false;" href="" runat="server" id="EngageSignInLink"> Sign In </a>
<br />
<br />
<div class="container" id="BodyContainer" visible="false" runat="server">
    <h1>User Information for <asp:Literal ID="HeaderName" runat="server"></asp:Literal></h1>
    <div class="left">
        <div id="EngagePhoto" runat="server" visible="false">
            <asp:Image ID="UserPhoto" runat="server" />
        </div>
    </div>
    <div class="right">
        <div id="EngageFirstName" runat="server" visible="false"><span class="bold extrapaddingright">First Name:</span> </div>
        <div id="EngageLastName" runat="server" visible="false"><span class="bold extrapaddingright">Last Name:</span> </div>
        <div id="EngageUserName" runat="server" visible="false"><span class="bold extrapaddingright">Username:</span> </div>
        <div id="EngageEmail" runat="server" visible="false"><span class="bold extrapaddingright">Email:</span> </div><hr />
        <div id="EngageProvider" runat="server" visible="false"><span class="bold extrapaddingright">Provider:</span> </div>
        <div id="EngageAccessToken" runat="server" visible="false"><span class="bold extrapaddingright">Access Token (OAuth):</span> </div>        
        <div id="EngageIdentifier" runat="server" visible="false"><span class="bold extrapaddingright">Identifier:</span> </div>
        
    </div>
</div>
<br style="clear:both"/>
<div class="contactcontainer">
    <div id="EngageUserContacts" runat="server" visible="false">
        <asp:Xml ID="Contacts" TransformSource="Styles/main.xslt" runat="server"></asp:Xml>
    </div>
</div>
</asp:Content>
